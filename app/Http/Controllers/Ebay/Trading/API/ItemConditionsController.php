<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class ItemConditionsController extends \App\Http\Controllers\Ebay\OAuth\OAuthController
{
    protected $service;

    public function __construct()
    { }
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }
}
