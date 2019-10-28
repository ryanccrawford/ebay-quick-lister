<?php

namespace App\Http\Controllers\Ebay\Trading\API;

use \App\Http\Controllers\Controller;
use \Illuminate\Http\Request;
use \Hkonnet\LaravelEbay\EbayServices;
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
    { }

    /**
     * Display all listings of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $ebay_service = new \DTS\eBaySDK\Trading\Services\TradingService();


        $this->service = new $ebay_service->createTrading();
        return response(var_dump($this->service), 200);
        $page_num = $request->query('page_num') ? intval($request->query('page_num')) : 1;
        $limit = $request->query('limit') ? intval($request->query('limit')) : 10;
        echo $page_num;
        echo $limit;

        $serviceRequest = $this->getServiceRequest($request, 'trading');
        $serviceRequest->ActiveList = new Types\ItemListCustomizationType();
        $serviceRequest->ActiveList->Include = true;
        $serviceRequest->ActiveList->Pagination = new Types\PaginationType();
        $serviceRequest->ActiveList->Pagination->EntriesPerPage = $limit;
        $serviceRequest->ActiveList->Sort = Enums\ItemSortTypeCodeType::C_CURRENT_PRICE_DESCENDING;
        $serviceRequest->ActiveList->Pagination->PageNumber = intval($page_num);

        $serviceResponse = $this->service->getMyeBaySelling($serviceRequest);
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
        if ($serviceResponse->Ack !== 'Failure' && isset($serviceResponse->ActiveList)) {
            return response()
                ->json([
                    'sucess' => true,
                    'items' => $serviceResponse->ActiveList->ItemArray->Item,
                    'taotal_pages' => $serviceResponse->ActiveList->PaginationResult->TotalNumberOfPages
                ]);
        }
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
    public function getServiceRequest(Request $request, String $return = null)
    {

        $request = new Types\GetMyeBaySellingRequestType();
        $request->RequesterCredentials = new Types\CustomSecurityHeaderType();
        if ($request->session()->has('token')) {
            $this->token = session('token');
            $request->RequesterCredentials->eBayAuthToken = $this->token;
            return $request;
        }
        $scope = array(
            'https://api.ebay.com/oauth/api_scope',
            'https://api.ebay.com/oauth/api_scope/sell.inventory',
            'https://api.ebay.com/oauth/api_scope/sell.fulfillment'
        );

        session(['scope' => $scope]);
        session(['return' => $return]);
        return redirect('getauth');
    }
}
