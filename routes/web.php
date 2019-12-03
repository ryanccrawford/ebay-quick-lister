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

//Entry Point for public



Route::get(
    '/',
    function () {
        return view('welcome');
    }
);

Auth::routes();




Route::group(['middleware' => ['web']], function () {

    //Entry point from login
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');

    Route::get('listings', 'EbayInventoryController@index')->name('listings');

    Route::get('create', 'EbayInventoryController@create')->name('create');

    //Ebay OAuth
    Route::get('oauth', 'Ebay\OAuth\OAuthController@oauth')->name('oauth');
    Route::get('getauth', 'Ebay\OAuth\OAuthController@getauth')->name('getauth');
    Route::get('refreshauth', 'Ebay\OAuth\OAuthController@refreshauth')->name('refreshauth');

    //Inventory Locations
    Route::get('inventory/locations', 'EbayInventoryController@showlocations')->name('inventory/locations');
    Route::get('inventory/locations/add', 'EbayInventoryController@createInventoryLocation')->name('inventory/showlocationadd');
    Route::post('inventory/locations/saveadd', 'EbayInventoryController@saveInventoryLocation')->name('inventory/savelocation');



    //Trading
    Route::get('trading', 'Ebay\Trading\API\ItemsController@index')->name('trading');
    Route::get('trading/search', 'Ebay\Trading\API\ItemsController@index')->name('trading/search');
    Route::get('trading/edit', 'Ebay\Trading\API\ItemsController@show')->name('trading/edit');


    Route::put('trading/edit', 'Ebay\Trading\API\ItemsController@update')->name('trading/edit');

    Route::get('api/get/returnpolicies', 'Ebay\Trading\API\ItemsController@returnpolicies')->name('api/get/returnpolicies');
    Route::get('api/get/paymentpolicies', 'Ebay\Trading\API\ItemsController@paymentpolicies')->name('api/get/paymentpolicies');
    Route::get('api/get/shippingpolicies', 'Ebay\Trading\API\ItemsController@shippingpolicies')->name('api/get/shippingpolicies');


    Route::get('api/get/suggestions', 'Ebay\Trading\API\AjaxController@GetSuggestedCategories')->name('api/get/suggestions');
});






//selleritem
Route::get('addselleritem', 'selleritemController@create');
Route::post('addselleritem', 'selleritemController@store');
Route::get('selleritem', 'selleritemController@index');
Route::get('editselleritem/{id}', 'selleritemController@edit');
Route::post('editselleritem/{id}', 'selleritemController@update');
Route::delete('selleritem/{id}', 'selleritemController@destroy');


//Deployment
Route::post('deploy', 'DeployController@deploy');
