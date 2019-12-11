<?php

namespace App\Http\Controllers\Ebay\Trading;

use \Illuminate\Http\Request;
use \Exception;
use \Illuminate\Support\Facades\Cache;
use \App\Http\Requests\StoreItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use \App\SellerItem;
use \lluminate\Http\UploadedFile;
use \App\Http\Controllers\Ebay\OAuth\OAuthController;
use \DTS\eBaySDK\MerchantData\Enums\DetailLevelCodeType;
use \DTS\eBaySDK\Trading\Types\AddFixedPriceItemRequestType;

class EbayBaseController extends OAuthController
{

    public $AccountService;
    public $service;
    public $TradingService;
    public $InventoryService;
    public $AnalyticsService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
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

        return  $this->getService('getMyeBaySelling', $serviceRequest,  $apiName = "Trading", $serviceType = "TradingService");
    }

    public function GetItemResponse($itemId = '', $SKU = ''): \DTS\eBaySDK\Trading\Types\GetItemResponseType
    {
        $this->middleware('auth');
        $serviceRequest = new \DTS\eBaySDK\Trading\Types\GetItemRequestType();
        if (strlen($itemId)) {
            $serviceRequest->ItemID = $itemId;
        }
        if (strlen($SKU)) {
            $serviceRequest->SKU = $SKU;
        }
        $serviceRequest->DetailLevel = [\DTS\eBaySDK\Trading\Enums\DetailLevelCodeType::C_ITEM_RETURN_DESCRIPTION];


        $this->TradingService = new \DTS\eBaySDK\Trading\Services\TradingService(
            [
                'siteId' => '0',
                'authorization' => session('user_token'),
                'credentials' => $this->credentials
            ]
        );

        return $this->TradingService->getItem($serviceRequest);
    }

    public function activeView(Request $request)
    {
        $isActiveList = 0;
        $isSoldList = 0;
        $isActiveList = $request->query('isactivelist') ? intval($request->query('isactivelist')) : false;
        $isSoldList = !$isActiveList;
        $page_num = $request->query('page_num') ? intval($request->query('page_num')) : 1;
        $limit = $request->query('limit') ? intval($request->query('limit')) : 10;
        $showList = $isActiveList ? 'ActiveList' : 'SoldList';
        $showListurl = $isActiveList ? 'isactivelist' : 'issoldlist';
        $pagination = new \DTS\eBaySDK\Trading\Types\PaginationType();
        $pagination->EntriesPerPage = $limit;
        $pagination->PageNumber = $page_num;
        $include = [$showList];
        $mySellingResults = $this->GetMyeBaySelling($include, $pagination);


        if ($mySellingResults->Ack == 'Failure') {
            $Errors = $mySellingResults->Errors;
            $this->middleware('ebayauth');
            session(['return' => '/trading?' . $showListurl . '=1&page_num=1']);
            return redirect('getauth');
        }

        $itemArrayType = $showList === 'ActiveList' ? 'ItemArray' : 'OrderTransactionArray';
        $itemType = $showList === 'ActiveList' ? 'Item' : 'OrderTransaction';
        if ($mySellingResults->Ack !== 'Failure' && isset($mySellingResults->$showList->$itemArrayType)) {


            $itemsArray =  $mySellingResults->$showList->$itemArrayType->$itemType;
            if (sizeof($mySellingResults->$showList->$itemArrayType->$itemType) === 0) {
                $errors = array(
                    'error' => true,
                    'messages' => "No Items Found!"
                );
                return view('ebay.trading.listings.listingitems', compact('errors'));
            }

            $totalPages = 1;

            if (!$request->session()->has('totalPages') && isset($mySellingResults->$showList->PaginationResult)) {
                session(['totalPages' => self::getTotalPages($mySellingResults->$showList->PaginationResult)]);
                $totalPages = intval(session('totalPages'));
            } elseif ($request->session()->has('totalPages')) {
                $totalPages = intval(session('totalPages'));
            }

            $limitParameter = '&limit=' . $limit;

            $prev = $page_num > 5 ? $page_num - 5 : 1;

            $next = ($page_num  < ($totalPages - 10)) ? ($page_num + 5) + (5 - $prev) : $totalPages;
            $currentPage = $page_num;
            $afterCurrentPageLinks = self::afterCurrentPage($page_num, $totalPages, 10 - $prev, '/trading?' . $showListurl . '=1&page_num=', $limitParameter);
            $next_link = '/trading?' . $showListurl . '=1&page_num=' . $next . $limitParameter;
            $prev_link = '/trading?' . $showListurl . '=1&page_num=' . $prev . $limitParameter;
            $beforeCurrentPageLinks = self::beforeCurrentPage($page_num, $totalPages, 5, '/trading?' . $showListurl . '=1&page_num=', $limitParameter);

            return view('ebay.trading.listings.listingitems', compact('itemsArray', 'next_link', 'prev_link', 'totalPages', 'limit', 'currentPage', 'afterCurrentPageLinks', 'beforeCurrentPageLinks', 'isActiveList', 'isSoldList'));
        }

        $error = array(

            'messages' => "Unknown Error"
        );

        return view('ebay.trading.listings.listingitems', compact('error'));
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
            try {
                $this->AccountService = new \DTS\eBaySDK\Account\Services\AccountService(
                    [
                        'siteId' => '0',
                        'authorization' => session('user_token'),
                        'credentials' => $this->credentials,
                    ]
                );
            } catch (Exception $e) {
                $this->middleware('ebayauth');
                return redirect('getauth');
            }
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
            $this->middleware('ebayauth');
            return redirect('getauth');
        }
        if ($request->session()->has('retry') && intval(session('retry')) <= 2) {
            $r = intval(session('retry')) + 1;
            session(['retry' => $retryRoute]);
            $this->middleware('ebayauth');
            return redirect('getauth');
        }
        session()->forget('retry');
        return view($failRoute, compact('Errors'));
    }

    public function getService($serviceName, $serviceRequest = null,  $apiName = "Trading", $serviceType = "TradingService")
    {
        $this->middleware('ebayauth');
        // 'verifyAddFixedPriceItem', ($serviceRequest)
        $type = '\\DTS\\eBaySDK\\';
        $type .= $apiName;
        $type .= '\\Services\\';
        $type .= $serviceType;
        $serviceResponse = null;
        if ($this->$serviceType === null) {
            $this->$serviceType = new $type(
                [
                    'siteId' => '0',
                    'authorization' => session('user_token') ? session('user_token') : $this->credentials['authToken'],
                    'credentials' => $this->credentials
                ]
            );
        }
        if ($serviceRequest === null) {
            $serviceResponse = $this->$serviceType->$serviceName();
        } else {
            $serviceResponse = $this->$serviceType->$serviceName($serviceRequest);
        }
        return $serviceResponse;
    }

    public function GetVerifiedItem(SellerItem $item): \DTS\eBaySDK\Trading\Types\ItemType
    {
        $return = new \DTS\eBaySDK\Trading\Types\ItemType();

        $serviceRequest = new \DTS\eBaySDK\Trading\Types\VerifyAddFixedPriceItemRequestType();
        $serviceRequest->Item = new \DTS\eBaySDK\Trading\Types\ItemType();
        $serviceRequest->Item->AutoPay = true;
        $serviceRequest->Item->ConditionID = 1000;
        $serviceRequest->Item->Country = \DTS\eBaySDK\Trading\Enums\CountryCodeType::C_US;
        $serviceRequest->Item->Currency = \DTS\eBaySDK\Trading\Enums\CurrencyCodeType::C_USD;
        $serviceRequest->Item->StartPrice = new \DTS\eBaySDK\Trading\Types\AmountType();
        $serviceRequest->Item->StartPrice->currencyID = \DTS\eBaySDK\Trading\Enums\CurrencyCodeType::C_USD;
        $serviceRequest->Item->StartPrice->value = $item->price;
        $serviceRequest->Item->Description =  $item->descriptionEditorArea;
        $serviceRequest->Item->DispatchTimeMax = 1;
        $serviceRequest->Item->SKU =  $item->sku;
        $serviceRequest->Item->IncludeRecommendations = false;
        $serviceRequest->Item->InventoryTrackingMethod = \DTS\eBaySDK\Trading\Enums\InventoryTrackingMethodCodeType::C_SKU;
        $serviceRequest->Item->ListingType = \DTS\eBaySDK\Trading\Enums\ListingTypeCodeType::C_FIXED_PRICE_ITEM;
        $serviceRequest->Item->ListingDuration = \DTS\eBaySDK\Trading\Enums\ListingDurationCodeType::C_GTC;
        $serviceRequest->Item->Location = "Ashland, VA";
        //images
        $serviceRequest->Item->PictureDetails = new \DTS\eBaySDK\Trading\Types\PictureDetailsType();
        $imagePaths = [
            $this->url->to('/') . $imageName1,
            $this->url->to('/') . $imageName2
        ];

        $serviceRequest->Item->PictureDetails->PictureURL =  $imagePaths;
        //primary category
        $serviceRequest->Item->PrimaryCategory = new \DTS\eBaySDK\Trading\Types\CategoryType();
        $serviceRequest->Item->PrimaryCategory->CategoryID = $item->primaryCategory;
        //listing details
        $serviceRequest->Item->ProductListingDetails = new \DTS\eBaySDK\Trading\Types\ProductListingDetailsType();
        $serviceRequest->Item->ProductListingDetails->BrandMPN = new \DTS\eBaySDK\Trading\Types\BrandMPNType();
        $serviceRequest->Item->ProductListingDetails->BrandMPN->Brand = "3 Star Inc";
        $serviceRequest->Item->ProductListingDetails->BrandMPN->MPN = $item->sku;
        $serviceRequest->Item->Quantity = $item->qty;
        //package details
        $serviceRequest->Item->ShippingPackageDetails = new \DTS\eBaySDK\Trading\Types\ShipPackageDetailsType();
        $serviceRequest->Item->ShippingPackageDetail->PackageDepth =  $item->shippingHeight;
        $serviceRequest->Item->ShippingPackageDetail->PackageLength = $item->shippingLength;
        $serviceRequest->Item->ShippingPackageDetail->PackageWidth  = $item->shippingWidth;
        $serviceRequest->Item->ShippingPackageDetail->WeightMajor = count(explode(".", floatval($item->shippingWeight))) > 0 ? explode(".", floatval($item->shippingWeight))[0] : 0;
        $serviceRequest->Item->ShippingPackageDetail->WeightMinor  = count(explode(".", floatval($item->shippingWeight))) > 0 ? explode(".", floatval($item->shippingWeight))[1] : 0;
        //seller profiles used
        $serviceRequest->Item->SellerProfiles = new \DTS\eBaySDK\Trading\Types\SellerProfilesType();
        $serviceRequest->Item->SellerProfiles->SellerPaymentProfile = new \DTS\eBaySDK\Trading\Types\SellerPaymentProfileType();
        $serviceRequest->Item->SellerProfiles->SellerPaymentProfile->PaymentProfileID = $item->PaymentPoliciesResponse;
        $serviceRequest->Item->SellerProfiles->SellerReturnProfile = new \DTS\eBaySDK\Trading\Types\SellerReturnProfileType();
        $serviceRequest->Item->SellerProfiles->SellerReturnProfile->ReturnProfileID =  $item->ReturnPoliciesResponse;
        $serviceRequest->Item->SellerProfiles->SellerShippingProfile = new \DTS\eBaySDK\Trading\Types\SellerShippingProfileType();
        $serviceRequest->Item->SellerProfiles->SellerShippingProfile->ShippingProfileID =  $item->ShippingPoliciesResponse;
        $serviceRequest->Item->Site = SiteCodeType::C_US;
        $serviceRequest->Item->DispatchTimeMax = 1;
        //response
        $serviceResponse = $this->getService('verifyAddFixedPriceItem', ($serviceRequest));
        $return = $serviceResponse->Item
        if ($serviceResponse->Ack === 200) {
            return $return;
        }
    }


}
