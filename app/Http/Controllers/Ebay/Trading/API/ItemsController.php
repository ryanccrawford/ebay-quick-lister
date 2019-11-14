<?php

namespace App\Http\Controllers\Ebay\Trading\API;

use \Illuminate\Http\Request;
use DTS\eBaySDK\MerchantData\Enums\DetailLevelCodeType;
use Exception;
use Illuminate\Support\Facades\Cache;


class ItemsController extends \App\Http\Controllers\Ebay\OAuth\OAuthController
{


    public $service;
    public $config;
    public $AccountService;
    public $marketPlaceId;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $this->marketPlaceId = \DTS\eBaySDK\Account\Enums\MarketplaceIdEnum::C_EBAY_US;
    }

    /**
     * Display all listings of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->activeView($request);
    }

    public static function getTotalPages(\DTS\eBaySDK\Trading\Types\PaginationResultType $pagination)
    {
        return intval($pagination->TotalNumberOfPages);
    }

    public static function afterCurrentPage(int $currentPage, int $totalPages, int $maxPages, string $url, string $limit)
    {
        $pages = [];
        if (($currentPage + $maxPages) <= ($totalPages)) {
            $pages = [];
            while (count($pages) < $maxPages) {
                $p = ++$currentPage;
                $pages[] = array(
                    'link' => $url . $p . $limit,
                    'page' => $p
                );
            }

            return $pages;
        }

        while ($currentPage < ($totalPages)) {
            $p = ++$currentPage;
            $pages[] = array(
                'link' => $url . $p . $limit,
                'page' => $p
            );
        }

        return $pages;
    }

    public static function beforeCurrentPage(int $currentPage, int $totalPages, int $maxPages, string $url, string $limit)
    {
        $pages = [];
        if ((($currentPage - $maxPages) > 1) && ($maxPages < ($totalPages))) {
            $pages = [];
            while (count($pages) < $maxPages) {
                $p = --$currentPage;
                $pages[] = array(
                    'link' => $url . $p . $limit,
                    'page' => $p
                );
            }

            return $pages;
        }

        while ($currentPage > 1 && $currentPage < ($totalPages)) {
            $p = --$currentPage;
            $pages[] = array(
                'link' => $url . $p . $limit,
                'page' => $p
            );
        }
        sort($pages);
        return $pages;
    }

    public function GetMyeBaySelling(array $include = [], \DTS\eBaySDK\Trading\Types\PaginationType $pageination = null, DetailLevelCodeType $detailLevel = null, $outputSelector = []): \DTS\eBaySDK\Trading\Types\GetMyeBaySellingResponseType
    {
        //DetailLevel
        //OutputSelector

        $onlyInclude = ['ActiveList', 'DeletedFromSoldList', 'DeletedFromUnsoldList', 'ScheduledList', 'SellingSummary', 'SoldList', 'UnsoldList'];

        //Creates a new Request
        $serviceRequest = new \DTS\eBaySDK\Trading\Types\GetMyeBaySellingRequestType();

        //Includes
        foreach ($include as $item) {

            if (!in_array($item, $onlyInclude)) {
                $error = $item . ' is not one of ' . implode(", ", $onlyInclude);
                throw new Exception($error);
            }

            $class = '\\DTS\\eBaySDK\\Trading\\Types\\ItemListCustomizationType';
            $serviceRequest->$item = new $class();
            $serviceRequest->$item->Include = true;

            if (strpos($item, 'List') && $pageination != null) {
                $serviceRequest->$item->Pagination = $pageination;
            }
        }

        //Detail Level
        if ($detailLevel != null) {
            $serviceRequest->DetailLevel = $detailLevel;
        }

        //Output Selector
        if (count($outputSelector) > 0) {
            $serviceRequest->OutputSelector = $outputSelector;
        }

        //

        $this->service = new \DTS\eBaySDK\Trading\Services\TradingService(
            [
                'siteId' => '0',
                'authorization' => session('user_token'),
                'credentials' => $this->credentials
            ]
        );

        return $this->service->getMyeBaySelling($serviceRequest);
    }

    public function GetItem($itemId = '', $SKU = ''): \DTS\eBaySDK\Trading\Types\GetItemResponseType
    {
        $serviceRequest = new \DTS\eBaySDK\Trading\Types\GetItemRequestType();
        if (strlen($itemId)) {
            $serviceRequest->ItemID = $itemId;
        }
        if (strlen($SKU)) {
            $serviceRequest->SKU = $SKU;
        }
        $serviceRequest->DetailLevel = [\DTS\eBaySDK\Trading\Enums\DetailLevelCodeType::C_ITEM_RETURN_DESCRIPTION];


        $this->service = new \DTS\eBaySDK\Trading\Services\TradingService(
            [
                'siteId' => '0',
                'authorization' => session('user_token'),
                'credentials' => $this->credentials
            ]
        );

        return $this->service->getItem($serviceRequest);
    }



    public function activeView(Request $request)
    {

        $page_num = $request->query('page_num') !== null  ? intval($request->query('page_num')) : 1;
        $limit = $request->query('limit') !== null ? intval($request->query('limit')) : 10;

        $pagination = new \DTS\eBaySDK\Trading\Types\PaginationType();
        $pagination->EntriesPerPage = $limit;
        $pagination->PageNumber = $page_num;
        $include = ['ActiveList'];


        if (session('user_token') === null || $this->isTokenExpired()) {
            $this->doOAuth(url()->full());
            return redirect('getauth');
        }


        $mySellingResults = $this->GetMyeBaySelling($include, $pagination);


        if ($mySellingResults->Ack == 'Failure') {
            $Errors = $mySellingResults->Errors;

            if ($Errors[0]->ErrorCode === '21917053') {
                return redirect('getauth');
            } else {
                dump($Errors);
                return view('ebay.trading.listings.listingitems', compact('Errors'));
            }
        }
        if ($mySellingResults->Ack !== 'Failure' && isset($mySellingResults->ActiveList->ItemArray)) {

            if (sizeof($mySellingResults->ActiveList->ItemArray->Item) === 0) {
                $errors = array(
                    'error' => true,
                    'messages' => "No Items Found!"
                );
                return view('ebay.trading.listings.listingitems', compact('errors'));
            }

            $totalPages = 1;

            if (!$request->session()->has('totalPages') && isset($mySellingResults->ActiveList->PaginationResult)) {
                session(['totalPages' => self::getTotalPages($mySellingResults->ActiveList->PaginationResult)]);
            } elseif ($request->session()->has('totalPages')) {
                $totalPages = session('totalPages');
            }

            $limitParameter = '&limit=' . $limit . '';

            $prev = $page_num > 5 ? $page_num - 5 : 1;

            $next = ($page_num  < ($totalPages - 10)) ? ($page_num + 5) + (5 - $prev) : $totalPages;
            $currentPage = $page_num;
            $afterCurrentPageLinks = self::afterCurrentPage($page_num, $totalPages, 10 - $prev, '/trading?isactiveview=1&page_num=', $limitParameter);
            $next_link = '/trading?isactiveview=1&page_num=' . $next . $limitParameter;
            $prev_link = '/trading?isactiveview=1&page_num=' . $prev . $limitParameter;
            $beforeCurrentPageLinks = self::beforeCurrentPage($page_num, $totalPages, 5, '/trading?isactiveview=1&page_num=', $limitParameter);
            $itemsArray =  $mySellingResults->ActiveList->ItemArray->Item;
            return view('ebay.trading.listings.listingitems', compact('itemsArray', 'next_link', 'prev_link', 'totalPages', 'limit', 'currentPage', 'afterCurrentPageLinks', 'beforeCurrentPageLinks'));
        }

        $error = array(

            'messages' => "Unknown Error"
        );

        return view('ebay.trading.listings.listingitems', compact('error'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreItem $request)
    {
        $validated = $request->validated();

        $serviceRequest = new \DTS\eBaySDK\Trading\Types\VerifyAddFixedPriceItemRequestType();
        $serviceRequest->Item = new \DTS\eBaySDK\Trading\Types\ItemType();
        $serviceRequest->Item->AutoPay = true;
        $serviceRequest->Item->ConditionID = 1000;
        $serviceRequest->Item->Country = \DTS\eBaySDK\Trading\Enums\CountryCodeType::C_US;
        $serviceRequest->Item->Currency = \DTS\eBaySDK\Trading\Enums\CurrencyCodeType::C_USD;
        $serviceRequest->Item->BuyItNowPrice = new \DTS\eBaySDK\Trading\Types\AmountType();
        $serviceRequest->Item->BuyItNowPrice->currencyID = \DTS\eBaySDK\Trading\Enums\CurrencyCodeType::C_USD;
        $serviceRequest->Item->BuyItNowPrice->value = doubleval(trim($request->input('price')));
        $serviceRequest->Item->Description =  trim($request->input('descriptionEditorArea'));
        $serviceRequest->Item->DispatchTimeMax = 1;
        $serviceRequest->Item->SKU =  trim($request->input('sku'));
        $serviceRequest->Item->IncludeRecommendations = false;
        $serviceRequest->Item->InventoryTrackingMethod = \DTS\eBaySDK\Trading\Enums\InventoryTrackingMethodCodeType::C_SKU;
        $serviceRequest->Item->ListingType = \DTS\eBaySDK\Trading\Enums\ListingTypeCodeType::C_FIXED_PRICE_ITEM;
        $serviceRequest->Item->ListingDuration = \DTS\eBaySDK\Trading\Enums\ListingDurationCodeType::C_GTC;
        $serviceRequest->Item->Location = "Ashland, VA";

        $serviceRequest->Item->PictureDetails = new \DTS\eBaySDK\Trading\Types\PictureDetailsType();

        $imagePaths = [
            'main' => '',
            'description' => '',
        ];

        $isMainImage = request()->input('mainImageFile') ? true : false;
        $isDescriptionImage =  request()->input('descriptionImageFile') ? true : false;

        if ($isMainImage) {

            request()->validate(
                [
                    'mainImageFile' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                ]
            );
            $mainImageFileName = time() . '.' . request()->image->getClientOriginalExtension();

            $imagePaths['main'] = $mainImageFileName;
            dump($imagePaths['main']);
            request()->image->move(public_path('images'), $mainImageFileName);
        }
        if ($isDescriptionImage) {

            request()->validate(
                [
                    'descriptionImageFile' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                ]
            );
            $descriptionImageFileName = time() . '.' . request()->image->getClientOriginalExtension();

            $imagePaths['description'] = $mainImageFileName;
            dump($imagePaths['description']);
            request()->image->move(public_path('images'), $descriptionImageFileName);
        }
        $images = [];
        foreach ($imagePaths as $key => $value) {
            if ($key === 'main') {
                $images[0] = $value;
            } else {
                $images[1] = $value;
            }
        }


        $serviceRequest->Item->PrimaryCategory = new \DTS\eBaySDK\Trading\Types\CategoryType();
        $serviceRequest->Item->PrimaryCategory->CategoryID = "32834";
        $serviceRequest->Item->ProductListingDetails = new \DTS\eBaySDK\Trading\Types\ProductListingDetailsType();
        $serviceRequest->Item->ProductListingDetails->BrandMPN = new \DTS\eBaySDK\Trading\Types\BrandMPNType();
        $serviceRequest->Item->ProductListingDetails->BrandMPN->Brand = "3 Star Inc";
        $serviceRequest->Item->ProductListingDetails->BrandMPN->MPN = $request->input('sku');
        $serviceRequest->Item->Quantity = intval($request->input('qty'));


        if (session('user_token') === null) {
            $this->doOAuth(url()->current());
            return redirect('getauth');
        }


        return redirect(url()->previous());

        //$serviceRequest->Item->ConditionID =
    }


    /**
     * Display one specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $item_id = $request->query('item_id');
        $create = $request->query('create');
        if ($create === 'true') {
            $descriptionTemplate = file_get_contents(public_path() . '/files/policy.html');
            if ($this->AccountService === null) {
                $this->AccountService = new \DTS\eBaySDK\Account\Services\AccountService(
                    [
                        'siteId' => '0',
                        'authorization' => session('user_token'),
                        'credentials' => $this->credentials,
                    ]
                );
            }

            return view('ebay.trading.listings.listingitemcreate', compact('descriptionTemplate'));
        }
        $itemResponse = $this->GetItem($item_id);
        $item = $itemResponse->Item;

        if ($item instanceof \DTS\eBaySDK\Trading\Types\ItemType) {
            return view('ebay.trading.listings.listingitemedit', compact('item'));
        }
        $Errors = [
            'message' => 'Unknown Error. Can not view item ' . $item_id,

        ];

        if ($create === 'true') {


            return $this->retry($request, 'trading/edit?create=true', 'ebay.trading.listings.listingitemcreate');
        }
        return $this->retry($request, 'trading/edit?item_id=' . $item_id, 'ebay.trading.listings.listingitemedit');
    }

    public function shippingpolicies(Request $request)
    {
        $label = 'Shipping Policy';
        $responseName = 'ShippingPoliciesResponse';
        $className = 'FulfillmentPolicies';
        $idName = 'fulfillmentPolicyId';
        $name = 'name';
        $view = $this->getSelectOptions($label, $responseName, $className, $idName, $name);
        return response($view, 200);
    }

    public function returnpolicies(Request $request)
    {
        $label = 'Return Policy';
        $responseName = 'ReturnPoliciesResponse';
        $className = 'ReturnPolicies';
        $idName = 'returnPolicyId';
        $name = 'name';
        $view = $this->getSelectOptions($label, $responseName, $className, $idName, $name);
        return response($view, 200);
    }
    
    public function paymentpolicies(Request $request)
    {
        $label = 'Payment Policy';
        $responseName = 'PaymentPoliciesResponse';
        $className = 'PaymentPolicies';
        $idName = 'paymentPolicyId';
        $name = 'description';
        $view = $this->getSelectOptions($label, $responseName, $className, $idName, $name);
        return response($view, 200);
    }

    public function getSelectOptions($label, $responseName, $className, $idName, $name)
    {
        $nameSpace = '\\DTS\\eBaySDK\\Account\\Types\\';
        $typeClass = $nameSpace . 'Get' . $className . 'ByMarketplaceRestRequest';
        $type = $typeClass;

        if ($this->AccountService === null) {
            $this->AccountService = new \DTS\eBaySDK\Account\Services\AccountService(
                [
                    'siteId' => '0',
                    'authorization' => session('user_token'),
                    'credentials' => $this->credentials,
                ]
            );
        }
        $Request = new $type();
        $Request->marketplace_id = $this->marketPlaceId;
        $function = 'get' . $className . "ByMarketplace";
        $responseObj = $this->AccountService->$function($Request);

        return view('ebay.partials.ajaxselectoptionfill', compact('label', 'responseName', 'className', 'idName', 'name', 'responseObj'))->render();
    }

    public function retry(Request $request, string $retryRoute, string $failRoute)
    {

        if (!$request->session()->has('retry')) {
            session(['retry' => 1]);
            session(['return' => $retryRoute]);
            return redirect('getauth');
        }
        if ($request->session()->has('retry') && intval(session('retry')) <= 2) {
            $r = intval(session('retry')) + 1;
            session(['retry' => $retryRoute]);
            return redirect('getauth');
        }
        session()->forget('retry');
        return view($failRoute, compact('Errors'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
