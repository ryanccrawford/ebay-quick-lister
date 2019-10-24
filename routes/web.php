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


Route::get(
    '/',
    function () {
        return view('welcome');
    }
);

Auth::routes();

Route::get('home', 'HomeController@index')->name('home');

Route::get('listings', 'EbayInventoryController@index')->name('listings');

Route::get('create', 'EbayInventoryController@create')->name('create');
Route::get('oauth', 'EbayInventoryController@oauth')->name('oauth');
Route::get('oauth/clear', 'EbayInventoryController@oauth')->name('oauth/clear');

Route::get('inventory/locations', 'EbayInventoryController@showlocations')->name('inventory/locations');
Route::get('inventory/locations/add', 'EbayInventoryController@createInventoryLocation')->name('inventory/showlocationadd');
Route::post('inventory/locations/saveadd', 'EbayInventoryController@saveInventoryLocation')->name('inventory/savelocation');