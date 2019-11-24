<?php

namespace App\Http\Controllers\Ebay\Trading\API;

use \Illuminate\Http\Request;
use DTS\eBaySDK\Trading\Services;
use Exception;
use Illuminate\Support\Facades\Cache;
use \App\Http\Controllers\Ebay\Trading\EbayItemBaseController;

class AjaxController extends EbayItemBaseController
{

    public $AccountService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        //$this->middleware('auth');
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

    public function shippingpolicies(Request $request)
    {
        $label = 'Shipping Policy';
        $responseName = 'ShippingPoliciesResponse';
        $className = 'FulfillmentPolicies';
        $idName = 'fulfillmentPolicyId';
        $name = 'name';
        $view = $this->getSelectOptions($label, $responseName, $className, $idName, $name);
        return response($view, 200);
    }

    public function returnpolicies(Request $request)
    {
        $label = 'Return Policy';
        $responseName = 'ReturnPoliciesResponse';
        $className = 'ReturnPolicies';
        $idName = 'returnPolicyId';
        $name = 'name';
        $view = $this->getSelectOptions($label, $responseName, $className, $idName, $name);
        return response($view, 200);
    }

    public function paymentpolicies(Request $request)
    {
        $label = 'Payment Policy';
        $responseName = 'PaymentPoliciesResponse';
        $className = 'PaymentPolicies';
        $idName = 'paymentPolicyId';
        $name = 'description';
        $view = $this->getSelectOptions($label, $responseName, $className, $idName, $name);
        return response($view, 200);
    }

    public function getSelectOptions($label, $responseName, $className, $idName, $name)
    {
        $nameSpace = '\\DTS\\eBaySDK\\Account\\Types\\';
        $typeClass = $nameSpace . 'Get' . $className . 'ByMarketplaceRestRequest';
        $type = $typeClass;

        if ($this->AccountService === null) {
            try {
                $this->AccountService = new \DTS\eBaySDK\Account\Services\AccountService(
                    [
                        'siteId' => '0',
                        'authorization' => session('user_token'),
                        'credentials' => $this->credentials,
                    ]
                );
            } catch (Exception $e) {
                $this->doOAuth(url()->current());
                return redirect('getauth');
            }
        }
        $Request = new $type();
        $Request->marketplace_id = $this->marketPlaceId;
        $function = 'get' . $className . "ByMarketplace";
        $responseObj = $this->AccountService->$function($Request);

        return view('ebay.partials.ajaxselectoptionfill', compact('label', 'responseName', 'className', 'idName', 'name', 'responseObj'))->render();
    }
}
