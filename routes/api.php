<?php

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

Route::group(array('prefix' => 'vendor'), function () {
    Route::post('getvendorlist', 'VendorController@getVendorList'); 
    Route::post('getproducts', 'VendorController@getProductList');
});
Route::group(array('prefix' => 'order'), function () {
    Route::post('blockorder', 'OrderController@blockOrder');  
});
Route::group(array('prefix' => 'user'), function () {
    Route::post('auth', 'UserController@auth');
});