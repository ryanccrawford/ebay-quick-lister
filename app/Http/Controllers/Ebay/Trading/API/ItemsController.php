<?php

namespace App\Http\Controllers\Ebay\Trading\API;

use \App\Http\Controllers\Controller;
use DateInterval;
use DateTime;
use \Illuminate\Http\Request;
use \DTS\eBaySDK\Trading\Services;
use \DTS\eBaySDK\Trading\Types;
use \DTS\eBaySDK\Trading\Enums;
use \DTS\eBaySDK\Constants;
use DTS\eBaySDK\MerchantData\Enums\DetailLevelCodeType;
use Exception;
use Illuminate\Database\Query\Expression;
use stdClass;
use Symfony\Component\Translation\Interval;

class ItemsController extends Controller
{


    protected $service;
    protected $config;
    protected $token;
    protected $credentials;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->credentials = [
            'appId' => env('EBAY_PROD_APP_ID'),
            'certId' => env('EBAY_PROD_CERT_ID'),
            'devId' => env('EBAY_PROD_DEV_ID')
        ];
    }

    /**
     * Display all listings of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->session()->has('user_token')) {
            return $this->activeView($request);
        } else {
            $this->doOAuth();
            return redirect('getauth');
        }
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
        dump($this->service);
        return $this->service->getItem($serviceRequest);
    }

    public function doOAuth($return = 'trading')
    {
        session()->forget('totalPages');
        session()->forget('user_token');
        session()->forget('scope');
        session()->forget('return');
        $this->prepareOAuthSession($return . '?isactiveview=1');
    }

    public function activeView(Request $request)
    {

        $page_num = $request->query('page_num') !== null  ? intval($request->query('page_num')) : 1;
        $limit = $request->query('limit') !== null ? intval($request->query('limit')) : 10;

        $pagination = new \DTS\eBaySDK\Trading\Types\PaginationType();
        $pagination->EntriesPerPage = $limit;
        $pagination->PageNumber = $page_num;
        $include = ['ActiveList'];

        $mySellingResults = $this->GetMyeBaySelling($include, $pagination);


        if ($mySellingResults->Ack == 'Failure') {
            $Errors = $mySellingResults->Errors;
            dump($Errors);
            return view('ebay.trading.listings.listingitems', compact('Errors'));
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
        echo var_dump($mySellingResults);
        return view('ebay.trading.listings.listingitems', compact('error'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $serviceRequest = new \DTS\eBaySDK\Trading\Types\
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
        if($create === 'true'){
            $descriptionTemplate = file_get_contents( public_path() . '/files/policy.html');
           return view('ebay.trading.listings.listingitemcreate',compact('descriptionTemplate'));
        }
        $itemResponse = $this->GetItem($item_id);
        $item = $itemResponse->Item;
        dump($item);
        if ($item instanceof \DTS\eBaySDK\Trading\Types\ItemType) {
            return view('ebay.trading.listings.listingitemedit', compact('item'));
        }
        $Errors = [
            'message' => 'Unknown Error. Can not view item ' . $item_id,

        ];
        dump($Errors);
        return $this->retry($request, 'trading/edit?item_id=' . $item_id, 'ebay.trading.listings.listingitemedit');
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

    /**
     * Do OAuth.
     *
     * @return void
     */
    public function prepareOAuthSession(String $return = '')
    {

        $scope = 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account';

        session(['scope' => $scope]);
        if ($return === '') {
            $return = 'home';
        }
        session(['return' => $return]);
    }
}
