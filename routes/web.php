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

//Entry point from login
Route::get('dashboard', 'HomeController@index')->name('dashboard');

Route::get('listings', 'EbayInventoryController@index')->name('listings');

Route::get('create', 'EbayInventoryController@create')->name('create');

//Ebay OAuth
Route::get('oauth', 'Ebay\OAuth\OAuthController@oauth')->name('oauth');
Route::get('getauth', 'Ebay\OAuth\OAuthController@getauth')->name('getauth');


//Inventory Locations
Route::get('inventory/locations', 'EbayInventoryController@showlocations')->name('inventory/locations');
Route::get('inventory/locations/add', 'EbayInventoryController@createInventoryLocation')->name('inventory/showlocationadd');
Route::post('inventory/locations/saveadd', 'EbayInventoryController@saveInventoryLocation')->name('inventory/savelocation');

//Trading
Route::get('trading', 'Ebay\Trading\API\ItemsController@index')->name('trading');
Route::get('trading/search', 'Ebay\Trading\API\ItemsController@index')->name('trading/search');
Route::get('trading/edit', 'Ebay\Trading\API\ItemsController@show')->name('trading/edit');


Route::put('trading/edit', 'Ebay\Trading\API\ItemsController@update')->name('trading/edit');
Route::post('trading/edit', 'Ebay\Trading\API\ItemsController@store')->name('trading/edit');





//selleritem
Route::get('addselleritem', 'selleritemController@create');
Route::post('addselleritem', 'selleritemController@store');
Route::get('selleritem', 'selleritemController@index');
Route::get('editselleritem/{id}', 'selleritemController@edit');
Route::post('editselleritem/{id}', 'selleritemController@update');
Route::delete('selleritem/{id}', 'selleritemController@destroy');


//Deployment
Route::post('deploy', 'DeployController@deploy');
