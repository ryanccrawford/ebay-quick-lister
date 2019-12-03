<?php

namespace App\Http\Controllers\Ebay\Trading\API;

use Illuminate\Http\Request;
use \App\Http\Controllers\Ebay\Trading\EbayBaseController;

class ItemConditionsController extends EbayBaseController
{


    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
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
