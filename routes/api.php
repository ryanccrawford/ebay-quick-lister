<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('imagepost', 'ImageUploadController@imagepost')->name('imagepost');

Route::post('api/quickupdate/price', 'QuickUpdateController@price')->name('api/quickupdate/price');
Route::post('api/quickupdate/qoh', 'QuickUpdateController@qoh')->name('api/quickupdate/qoh');

// Route::get('api/get/returnpolicies', 'Ebay\Trading\API\ItemsController@returnpolicies')->name('api/get/returnpolicies');
// Route::get('api/get/paymentpolicies', 'Ebay\Trading\API\ItemsController@paymentpolicies')->name('api/get/paymentpolicies');
