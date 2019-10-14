<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('home', 'HomeController@index')->name('home');
Route::get('authReturn', function ($request) {
    $code = $request->input('cade');
    echo print_r($code, true);
    if(!strlen($code) && !strlen($this->configuation['production']['oauthUserToken'])){
        return redirect()->away('https://auth.ebay.com/oauth2/authorize?client_id=RyanCraw-7191-4ec8-b38a-6e7ab0cf5091&response_type=code&redirect_uri=Ryan_Crawford-RyanCraw-7191-4-xofian&scope=https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute');
    }
    
    if(strlen($code)) {
        $authEndpoint = 'https://api.ebay.com/identity/v1/oauth2/token';
        $this->configuation['production']['oauthUserToken'] = '';
        $enocoded = base64_encode($this->configuation['production']['appId'] . ":" . $this->configuation['production']['certId']);
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ' . $enocoded
        ];

        $bodyData = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->configuation['production']['ruName']
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $authEndpoint);
        curl_setopt($curl, CURLOPT_HTTPHEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS , $bodyData);
        $resp = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($resp);
        $decodedOAuthToken =  $json->access_token;
        $this->configuation['production']['oauthUserToken'] = $decodedOAuthToken;
        echo $decodedOAuthToken;
        die();

        $this->inventoryService = new Services\InventoryService(
        [
                'authorization' => $this->configuation['production']['oauthUserToken']
        ]
        );
   }
   if(strlen($this->configuation['production']['oauthUserToken'])){
    
   
    $request = new \DTS\eBaySDK\Inventory\Types\GetInventoryItemsRestRequest();

    $response = $this->inventoryService->getInventoryItems($request);
    echo print_r($response, true);
   }
    
    return $name;



});


Route::get('show', 'ShowListingsController@show')->name('show');

