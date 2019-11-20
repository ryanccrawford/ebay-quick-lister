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


class EbayItemBaseController extends OAuthController
{

    public $AccountService;
    public $service;

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
        $this->middleware('auth');

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
                $this->doOAuth(url()->current());
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

    public function getService($serviceName, $serviceRequest,  $apiName = "Trading", $serviceType = "TradingService")
    {

        // 'verifyAddFixedPriceItem', ($serviceRequest)
        $type = '\\DTS\\eBaySDK\\';
        $type .= $apiName;
        $type .= '\\Services\\';
        $type .= $serviceType;

        if ($this->service === null) {
            $this->service = new $type(
                [
                    'siteId' => '0',
                    'authorization' => session('user_token'),
                    'credentials' => $this->credentials
                ]
            );
        }

        return $this->service->$serviceName($serviceRequest);
    }
}
