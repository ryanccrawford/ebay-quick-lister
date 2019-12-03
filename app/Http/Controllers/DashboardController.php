<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Ebay\Trading\EbayBaseController;
use Illuminate\Http\Request;
use \DTS\eBaySDK\Analytics\Services;
use \DTS\eBaySDK\Analytics\Types;
use \DTS\eBaySDK\Analytics\Enums;


class DashboardController extends EbayBaseController
{

    public $dashboard;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->middleware('auth');
        $this->dashboard = $this->getDashboard();
        $dashboard = $this->dashboard;
        if ($dashboard['redirect']) {
            return response()->redirectTo('getauth');
        }
        $dash =  $dashboard['dash'];
        return view('dashboard', compact('dash'));
    }

    public function getDashboard()
    {

        $this->middleware('ebayauth');

        $serviceResponse = new \DTS\eBaySDK\Analytics\Types\GetAllSellerProfilesRestResponse();
        $serviceResponse = $this->getService('getAllSellerProfiles', null, "Analytics", "AnalyticsService");
        if (count($serviceResponse->errors) > 0) {
            session()->forget('user_token');
            session(['scope' => $this->scope]);
            session(['return' => '/dashboard']);
            dump($serviceResponse);
            return ['redirect' => true];
        }

        $dash = [];
        foreach ($serviceResponse->standardsProfiles as $profile) {
            $dash['profile'] = $profile;

            $metrics = [];
            foreach ($profile->metrics as $metric) {

                $metrics[$metric->name] = $metric->metricKey;
            }
            $dash['metrics'] = $metrics;
        }
        $trafficReport = [];
        $trafficServiceRequest = new \DTS\eBaySDK\Analytics\Types\GetTrafficReportRestRequest();
        $trafficServiceRequest->dimension = ['DAY', 'LISTING'];
        $trafficServiceRequest->metric = ['CLICK_THROUGH_RATE', 'LISTING_IMPRESSION_SEARCH_RESULTS_PAGE', 'LISTING_IMPRESSION_STORE', 'LISTING_IMPRESSION_TOTAL', 'LISTING_VIEWS_SOURCE_DIRECT', 'LISTING_VIEWS_SOURCE_OFF_EBAY', 'LISTING_VIEWS_SOURCE_OTHER_EBAY', 'LISTING_VIEWS_SOURCE_SEARCH_RESULTS_PAGE', 'LISTING_VIEWS_SOURCE_STORE', 'LISTING_VIEWS_TOTAL', 'SALES_CONVERSION_RATE', 'TRANSACTION'];

        return ['dash' => $dash, 'redirect' => false];
    }
}
