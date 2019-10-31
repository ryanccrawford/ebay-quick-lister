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
            'appId' => getenv('EBAY_PROD_APP_ID'),
            'certId' => getenv('EBAY_PROD_CERT_ID'),
            'devId' => getenv('EBAY_PROD_DEV_ID'),
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

            $listingItems = $this->getMyeBaySellingItems($page_num, $limit, $request);

            if(!$listingItems instanceof \DTS\eBaySDK\Trading\Types\GetMyeBaySellingResponseType){
              
                
                if(isset($listingItems->error)){
                   
                    $errorsObj = $listingItems->messages;
                    return view('ebay.trading.listings.listingitems', compact('errorsObj'));
                }else{
                        $errors = array(
                           
                                'error' => true,
                                'messages' => "No Items Found!"
                            
                        );
                }
            
                return view('ebay.trading.listings.listingitems', compact('errors'));
            }
            
           
            $totalPages = 1;

            if(!$request->session()->has('totalPages')){
                $totalPages = self::getTotalPages($listingItems);
                session(['totalPages' => $totalPages]);
            }else{
                $totalPages = session('totalPages');
            }
            $limitParameter = '&limit='.$limit.'';
            $next = ($page_num  < ($totalPages - 5)) ? $page_num + 5 : $totalPages;
            $prev = $page_num > 5 ? $page_num - 5 : 1;
            $currentPage = $page_num;
            $afterCurrentPageLinks = self::afterCurrentPage($page_num, $totalPages, 5, '/trading?page_num=',  $limitParameter);
            $next_link = '/trading?page_num=' . $next . $limitParameter;
            $prev_link = '/trading?page_num=' . $prev . $limitParameter;
            $beforeCurrentPageLinks = self::beforeCurrentPage($page_num, $totalPages, 5, '/trading?page_num=',  $limitParameter);
           
            $listingArray =  $listingItems->ActiveList->ItemArray->Item;
            echo "Showing Listings";
            return view('ebay.trading.listings.listingitems', compact('listingArray', 'next_link', 'prev_link', 'totalPages', 'limit', 'currentPage', 'afterCurrentPageLinks', 'beforeCurrentPageLinks'));

        }
        echo "Getting new token";
            session()->forget('totalPages');
            session()->forget('token');
            session()->forget('scope');
            session()->forget('return');
            $this->getToken($request, 'trading', true);
            return redirect('getauth');
           
        
    }

    public static function getTotalPages($listingItems){
       
        return intval($listingItems->ActiveList->PaginationResult->TotalNumberOfPages);
    }

    public static function afterCurrentPage(int $currentPage, int $totalPages, int $maxPages, string $url, string $limit){
        $pages = [];
        if(($currentPage + $maxPages) <= ($totalPages)){
            $pages = [];
           while(count($pages) < $maxPages){
             $p = ++$currentPage;
                $pages[] = array( 
                    'link' => $url . $p . $limit,
                    'page' => $p
            );
            }

            return $pages;
        }
       
        while( $currentPage < ($totalPages)){
            $p = ++$currentPage;
            $pages[] = array( 
                'link' => $url . $p . $limit,
                'page' => $p
        );
        
        }

        return $pages;



    }

    public static function beforeCurrentPage(int $currentPage, int $totalPages, int $maxPages, string $url, string $limit){
        $pages = [];
        if((($currentPage - $maxPages) > 1) && ($maxPages < ($totalPages))){
            $pages = [];
           while(count($pages) < $maxPages){
                $p = --$currentPage;
                $pages[] = array( 
                    'link' => $url . $p . $limit,
                    'page' => $p
            );
            }

            return $pages;
        }
        
        while($currentPage > 1 && $currentPage < ($totalPages)){
            $p = --$currentPage;
            $pages[] = array( 
                'link' => $url . $p . $limit,
                'page' => $p
        );
        
        }
        sort($pages);
        return $pages;



    }

    public function getMyeBaySellingItems(Int $Page_number, Int $Page_limit, Request $request)
    {
       
        try {
            $this->service = new \DTS\eBaySDK\Trading\Services\TradingService(
                [
                    'siteId' => '0',
                    'authorization' => session('token')
                ]
            );
           
            $serviceRequest = new \DTS\eBaySDK\Trading\Types\GetMyeBaySellingRequestType();
            $serviceRequest->ActiveList = new \DTS\eBaySDK\Trading\Types\ItemListCustomizationType();
            $serviceRequest->ActiveList->Pagination = new \DTS\eBaySDK\Trading\Types\PaginationType();
            $serviceRequest->ActiveList->Pagination->PageNumber = $Page_number;
            $serviceRequest->ActiveList->Pagination->EntriesPerPage = $Page_limit;
             
            $ouputSelection = ['ActiveList', 'ActiveList.PaginationResult'];
            $serviceRequest->OutputSelector = $ouputSelection;
            $serviceResponse = $this->service->getMyeBaySelling($serviceRequest);
            
            if (isset($serviceResponse->Errors)) {
                session()->forget('token');
                $message = [];
                foreach ($serviceResponse->Errors as $error) {
                    $message[] = printf(
                        "%s: %s\n%s\n\n",
                        $error->SeverityCode === Enums\SeverityCodeType::C_ERROR ? 'Error' : 'Warning',
                        $error->ShortMessage,
                        $error->LongMessage
                    );
                }

                return json_encode([
                        'error' => true,
                        'messages' => $message
                    ]);
                die();
            }
            if ($serviceResponse->Ack !== 'Failure' && isset($serviceResponse->ActiveList->ItemArray)) {
                //$serviceResponse->ActiveList->ItemArray->Item[0]->QuantityAvailable;
                // \DTS\eBaySDK\Trading\Types\ItemType;
                return $serviceResponse;
            }
        }catch(Expression $e){

            session()->forget('totalPages');
            session()->forget('token');
            session()->forget('scope');
            session()->forget('return');
            session()->forget('token');
            $this->getToken($request, 'trading', true);
            return redirect('getauth');

        }
        


    }

    public function clearSessionPagination(){
        
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
