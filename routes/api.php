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

Route::group(array('namespace' => 'Locals', 'prefix' => 'locals'), function() {
    Route::post('addLocal', 'LocalsController@addLocal'); // checked MSC
    Route::post('removeLocal', 'LocalsController@removeLocal'); // checked MSC

    Route::post('addLocalToFavourite', 'LocalsController@addLocalToFavourite'); // checked MSC
    Route::post('removeLocalFromFavourite', 'LocalsController@removeLocalFromFavourite'); // checked MSC

    Route::post('addOpenDays', 'LocalsController@addOpenDays'); // checked MSC
    Route::post('addTagsToLocal', 'LocalsController@addTagsToLocal'); // checked MSC

    Route::get('getList/{id_city_const_type}', 'LocalsController@getList'); // checked MSC
    Route::get('getDetails/{id_local_data_main}', 'LocalsController@getDetails'); // checked MSC
    Route::get('getFavouriteList/{id_city_const_type}', 'LocalsController@getFavouriteList'); // checked MSC
  });

  Route::group(array('namespace' => 'Coupons', 'prefix' => 'coupons'), function() {
    Route::post('addCoupon', 'CouponsController@addCoupon'); // checked MSC
    Route::post('removeCoupon', 'CouponsController@removeCoupon'); // checked MSC

    Route::post('addCouponToFavourite', 'CouponsController@addCouponToFavourite'); // checked MSC
    Route::post('removeFromFavourite', 'CouponsController@removeFromFavourite'); // checked MSC
    
    Route::get('getList/{id_local_data_main}', 'CouponsController@getList'); // checked MSC
    Route::get('getDetails/{id_local_ref_coupon}', 'CouponsController@getDetails'); // checked MSC
    Route::get('getFavouriteList', 'CouponsController@getFavouriteList'); // checked MSC

    Route::post('orderCoupon', 'CouponsController@orderCoupon'); // checked MSC
    Route::post('checkCoupon', 'CouponsController@checkCoupon'); // checked MSC
  });

Route::group(['namespace' => 'Tools', 'prefix' => 'tools'], function() {
    Route::post('getList', 'SelectItemController@getList');
});

Route::group(['namespace' => 'Tags', 'prefix' => 'tags'], function() {
  Route::post('addTag', 'TagsController@addTag');
});

