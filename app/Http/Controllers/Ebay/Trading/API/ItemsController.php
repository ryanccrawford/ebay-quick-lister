<?php

namespace App\Http\Controllers\Ebay\Trading\API;

use \App\Http\Controllers\Controller;
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
                    'authorization' => session('token')
                ]
            );
            if (!$this->service) {
                echo 'no service';
                $this->getToken($request, 'trading', true);
            }
            $serviceRequest  = new  $this->service->getSellerList();
            return response(var_dump($this->service), 200);
            $page_num = $request->query('page_num') ? intval($request->query('page_num')) : 1;
            $limit = $request->query('limit') ? intval($request->query('limit')) : 10;
            echo $page_num;
            echo $limit;


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
        $this->getToken($request, 'trading', false);
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
        echo var_dump($request->session());
        if ($request->session()->has('token') && !$failed) {
            $this->token = session('token');
            return;
        }
        $scope = 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.fulfillment';


        session(['scope' => $scope]);
        if ($return === '') {
            $return = 'home';
        }
        session(['return' => $return]);
        return redirect('getauth');
    }
}
