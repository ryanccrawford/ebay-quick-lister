<?php

namespace App\Http\Controllers\Ebay\Trading\Web;

use \App\Http\Controllers\Controller;
use \Illuminate\Http\Request;
use \DTS\eBaySDK\Trading\Types;
use \DTS\eBaySDK\Trading\Enums;
use \DTS\eBaySDK\Constants;

class ItemsController extends \App\Http\Controllers\Ebay\OAuth\OAuthController
{


    protected $service;
    protected $config;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        if(!$request->session()->has('user_token')){
            
            return redirect('getauth');

        }
        
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
