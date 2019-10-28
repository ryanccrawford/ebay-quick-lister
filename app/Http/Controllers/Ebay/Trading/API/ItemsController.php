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

class ItemsController extends Controller
{


    protected $service;
    protected $config;
    protected $token;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all listings of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        
        if ($request->session()->has('token')) {
            echo 'have';
            $this->service = new \DTS\eBaySDK\Trading\Services\TradingService(
                [
                    'siteId' => '0',
                    'authorization' => session('token')
                ]
            );
           
            $serviceRequest = new \DTS\eBaySDK\Trading\Types\GetMyeBaySellingRequestType();
           
            $page_num = $request->query('page_num') ? intval($request->query('page_num')) : 1;
            $limit = $request->query('limit') ? intval($request->query('limit')) : 10;
             
            $serviceRequest->ActiveList = new \DTS\eBaySDK\Trading\Types\ItemListCustomizationType();
            $serviceRequest->ActiveList->Pagination = new \DTS\eBaySDK\Trading\Types\PaginationType();
            $serviceRequest->ActiveList->Pagination->PageNumber = $page_num;
            $serviceRequest->ActiveList->Pagination->EntriesPerPage = $limit;
             
           
            $serviceResponse = $this->service->getMyeBaySelling($serviceRequest);
            echo var_dump($serviceResponse);
            if (isset($serviceResponse->Errors)) {
                $message = array();
                foreach ($serviceResponse->Errors as $error) {
                    $message[] = printf(
                        "%s: %s\n%s\n\n",
                        $error->SeverityCode === Enums\SeverityCodeType::C_ERROR ? 'Error' : 'Warning',
                        $error->ShortMessage,
                        $error->LongMessage
                    );
                }

                return response()
                    ->json([
                        'error' => true,
                        'messages' => $message
                    ]);
            }
            if ($serviceResponse->Ack !== 'Failure' && isset($serviceResponse->ItemArray)) {
                return response()
                    ->json([
                        'sucess' => true,
                        'items' => $serviceResponse->ItemArray,
                        'taotal_pages' => $serviceResponse->PaginationResult->TotalNumberOfPages
                    ]);
            }
        }
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
        return redirect('getauth');
    }
}
