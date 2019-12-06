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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('imagepost', 'ImageUploadController@imagepost')->name('imagepost');


Route::post('trading/new', 'Ebay\Trading\API\ItemsController@store')->name('trading/new');
Route::post('trading/verify', 'Ebay\Trading\API\ItemsController@verify')->name('trading/verify');
