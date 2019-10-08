<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;
use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Trading\Types;
use \DTS\eBaySDK\Trading\Enums;
use \Hkonnet\LaravelEbay\EbayServices;
use Illuminate\Pagination\LengthAwarePaginator;

class ShowListingsController extends Controller
{

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        
        
        $ebay_service = new EbayServices();
        $service = $ebay_service->createTrading();

        $request = new Types\GetMyeBaySellingRequestType();

        $request->RequesterCredentials = new Types\CustomSecurityHeaderType();
        $authToken = Ebay::getAuthToken();
        $request->RequesterCredentials->eBayAuthToken = $authToken;

        $request->ActiveList = new Types\ItemListCustomizationType();
        $request->ActiveList->Include = true;
        $request->ActiveList->Pagination = new Types\PaginationType();
        $request->ActiveList->Pagination->EntriesPerPage = 10;
        $request->ActiveList->Pagination->PageNumber = 1; 
        $request->ActiveList->Sort = Enums\ItemSortTypeCodeType::C_CURRENT_PRICE_DESCENDING;
        /**
         * Send the request.
         */
            $response = $service->getMyeBaySelling($request);
          
            
            if (isset($response->Errors)) {
                
                $errors = $response->Errors;
                return view('ebay.listings.listings', compact('errors'));
                
            }
            if ($response->Ack !== 'Failure' && isset($response->ActiveList)) {
                $totalPages = $response->ActiveList->PaginationResult->TotalNumberOfPages;
                $pn = $response->ActiveList->PaginationResult->PageNumber;
                $listings = $response->ActiveList;

                return view('ebay.listings.listings', compact('listings','totalPages','pn'));
            }
           

      
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function edit(Listing $listing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Listing $listing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function destroy(Listing $listing)
    {
        //
    }
}
