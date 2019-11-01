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
use Illuminate\Database\Query\Expression;
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
            'devId' => env('EBAY_PROD_DEV_ID'),
        ];
    }

    /**
     * Display all listings of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

       
        
        if ($request->session()->has('token')) {

            $page_num = $request->query('page_num') !== null  ? intval($request->query('page_num')) : 1;
            $limit = $request->query('limit') !== null ? intval($request->query('limit')) : 10;
            echo "b4 service call";
            $listingItems = $this->getSellingList($page_num, $limit, $request);
            echo "b4 comparison";
            if (!$listingItems instanceof \DTS\eBaySDK\Trading\Types\GetSellerListResponseType) {

                echo "is an instanceof GetSellerList";
                if (isset($listingItems->error)) {

                    $errorsObj = $listingItems->messages;
                    return view('ebay.trading.listings.listingitems', compact('errorsObj'));
                } else {
                    $errors = array(

                        'error' => true,
                        'messages' => "No Items Found!"

                    );
                }

                return view('ebay.trading.listings.listingitems', compact('errors'));
            }


            $totalPages = 1;

            if (!$request->session()->has('totalPages')) {
                $totalPages = self::getTotalPages($listingItems);
                session(['totalPages' => $totalPages]);
            } else {
                $totalPages = session('totalPages');
            }
            $limitParameter = '&limit=' . $limit . '';
            $next = ($page_num  < ($totalPages - 5)) ? $page_num + 5 : $totalPages;
            $prev = $page_num > 5 ? $page_num - 5 : 1;
            $currentPage = $page_num;
            $afterCurrentPageLinks = self::afterCurrentPage($page_num, $totalPages, 5, '/trading?page_num=',  $limitParameter);
            $next_link = '/trading?page_num=' . $next . $limitParameter;
            $prev_link = '/trading?page_num=' . $prev . $limitParameter;
            $beforeCurrentPageLinks = self::beforeCurrentPage($page_num, $totalPages, 5, '/trading?page_num=',  $limitParameter);
           //$listingItems->ItemArray->Item[0]
            $listingArray =  $listingItems->ItemArray->Item;
            //echo "buy it now price";
            //echo var_dump($listingArray[0]->BuyItNowPrice);
            //echo var_dump($listingArray);
            return view('ebay.trading.listings.listingitems', compact('listingArray', 'next_link', 'prev_link', 'totalPages', 'limit', 'currentPage', 'afterCurrentPageLinks', 'beforeCurrentPageLinks'));
        }
        //echo "Getting new token";
        session()->forget('totalPages');
        session()->forget('token');
        session()->forget('scope');
        session()->forget('return');
        $this->getToken($request, 'trading', true);
        return redirect('getauth');
    }

    public static function getTotalPages($listingItems)
    {

        return intval($listingItems->PaginationResult->TotalNumberOfPages);
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

    public function getSellingList(Int $Page_number, Int $Page_limit, Request $request)
    {

        try {

         
            $this->service = new \DTS\eBaySDK\Trading\Services\TradingService(
                [
                    'siteId' => '0',
                    'authorization' => session('token'),
                    'credentials' => $this->credentials
                ]
            );

            $searchFor = $request->input('search');
           
            $serviceRequest = new \DTS\eBaySDK\Trading\Types\GetSellerListRequestType();
    
            $serviceRequest->EndTimeTo= new DateTime(now());
            $serviceRequest->EndTimeFrom  =  new DateTime(date('Y-m-d H:i:s', strtotime('-100 days', strtotime('now'))));
            $serviceRequest->DetailLevel =  [\DTS\eBaySDK\Trading\Enums\DetailLevelCodeType::C_RETURN_ALL];
            

            $serviceRequest->Pagination = new \DTS\eBaySDK\Trading\Types\PaginationType();
            $serviceRequest->Pagination->PageNumber = $Page_number ? $Page_number : 1;
            
            $serviceRequest->Pagination->EntriesPerPage = $Page_limit ? $Page_limit : 10;
            if ($searchFor) {
                $serviceRequest->SKUArray = new \DTS\eBaySDK\Trading\Types\SKUArrayType(explode(" ", $searchFor));
            }
            //echo var_dump($serviceRequest);
            //$ouputSelection = ['ItemArray.Item.BuyItNowPrice', 'PaginationResult', 'ItemArray.Item.Quantity'];
            //$serviceRequest->OutputSelector = $ouputSelection;
            $serviceResponse = $this->service->getSellerList($serviceRequest);
           
            if (isset($serviceResponse->Errors)) {
                $this->doOAuth($request);
            }
            if ($serviceResponse->Ack !== 'Failure' && isset($serviceResponse->ItemArray)) {
                //$serviceResponse->ActiveList->ItemArray->Item[0]->QuantityAvailable;
                // \DTS\eBaySDK\Trading\Types\ItemType;
                echo "Everything went well";
             
               
                return $serviceResponse;
            }
        } catch (Expression $e) {
            echo "Exception Was Triggered";
            echo var_dump($e);
            echo "Doing OAuth";
            $this->doOAuth($request);
        }
        echo "No exception but did not return an ItemArray oject";
        echo var_dump($serviceResponse);
    }

    public function doOAuth(Request $request)
    { 
        session()->forget('totalPages');
        session()->forget('token');
        session()->forget('scope');
        session()->forget('return');
        session()->forget('token');
        $this->getToken($request, 'trading', true);
        return redirect('getauth');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display one specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function getToken(Request $request, String $return = '', $failed = false)
    {

        $scope = 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account';

        session(['scope' => $scope]);
        if ($return === '') {
            $return = 'home';
        }
        session(['return' => $return]);
    }
}
