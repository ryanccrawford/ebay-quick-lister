<?php

namespace App\Http\Controllers\Ebay\Trading\Web;

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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $ebay_service = new EbayServices();
        $this->service = new $ebay_service->createTrading();
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
}
