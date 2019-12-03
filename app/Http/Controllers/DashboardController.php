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
        return view('dashboard', compact('dashboard'));
    }

    public function getDashboard()
    {

        $this->middleware('ebayauth');

        $serviceResponse = new \DTS\eBaySDK\Analytics\Types\GetAllSellerProfilesRestResponse();
        $serviceResponse = $this->getService('getAllSellerProfiles', null, "Analytics", "AnalyticsService");

        dump($serviceResponse);
        dump($this);
        return ['nothing' => 'at all'];
    }
}
