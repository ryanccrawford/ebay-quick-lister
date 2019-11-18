<?php

namespace App\Http\Controllers\Ebay\Trading\API;

use \Illuminate\Http\Request;
use DTS\eBaySDK\Trading\Services;
use Exception;
use Illuminate\Support\Facades\Cache;


class AjaxController extends \App\Http\Controllers\Ebay\OAuth\OAuthController
{


    public $service;
    public $config;
    public $AccountService;
    public $marketPlaceId;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth');
        $this->marketPlaceId = \DTS\eBaySDK\Account\Enums\MarketplaceIdEnum::C_EBAY_US;
    }

    public function GetSuggestedCategories(Request $request)
    {
        $itemTitle = trim($request->input('title'));

        if (!strlen($itemTitle) > 4 || strlen($itemTitle) > 350) {
            return response()->json(['warning' => 'Min char 4, max char 350 ']);
        }

        $serviceRequest = new \DTS\eBaySDK\Trading\Types\GetSuggestedCategoriesRequestType();

        $serviceRequest->Query = $itemTitle;

        $this->service = new \DTS\eBaySDK\Trading\Services\TradingService(
            [
                'siteId' => '0',
                'authorization' => session('user_token'),
                'credentials' => $this->credentials
            ]
        );

        $serviceResponse = $this->service->GetSuggestedCategories($serviceRequest);
        $cats =  $serviceResponse;
        $view = view('ebay.partials.ajaxsuggestedcategories', compact('cats'))->render();

        return $view;
    }
}
