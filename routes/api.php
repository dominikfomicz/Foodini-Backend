<?php

use Illuminate\Http\Request;
use Auth;
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

Route::get('/user', function () {
    $current_user = Auth::user();
    return $current_user;
})->middleware('auth:api');

Route::group(array('namespace' => 'Locals', 'prefix' => 'locals'), function() {
    Route::post('changeLocal', 'LocalsController@changeLocal'); // checked MSC
    Route::post('removeLocal', 'LocalsController@removeLocal'); // checked MSC

    Route::post('addLocalToFavourite', 'LocalsController@addLocalToFavourite'); // checked MSC
    Route::post('removeLocalFromFavourite', 'LocalsController@removeLocalFromFavourite'); // checked MSC

    Route::post('addOpenDays', 'LocalsController@addOpenDays'); // checked MSC
    Route::post('addTagsToLocal', 'LocalsController@addTagsToLocal'); // checked MSC

    Route::get('getList/{id_city_const_type}', 'LocalsController@getList'); // checked MSC
    Route::get('getDetails/{id_local_data_main}', 'LocalsController@getDetails'); // checked MSC
    Route::get('getFavouriteList/{id_city_const_type}', 'LocalsController@getFavouriteList'); // checked MSC
    Route::get('getMapList/{id_city_const_type}', 'LocalsController@getMapList'); // checked MSC

    Route::get('getDetailsEdit/{id_local_data_main}', 'LocalsController@getDetailsEdit'); // checked MSC

    Route::post('showFacebookCount', 'LocalsStatisticsController@showFacebookCount'); // checked MSC
    Route::post('showInstagramCount', 'LocalsStatisticsController@showInstagramCount'); // checked MSC
    Route::post('showMenuCount', 'LocalsStatisticsController@showMenuCount'); // checked MSC
    Route::post('showPhonenumberCount', 'LocalsStatisticsController@showPhonenumberCount'); // checked MSC

    Route::group(array('prefix' => 'files'), function() {
      Route::post('addLogo', 'FilesController@addLogo');
      Route::post('addBackground', 'FilesController@addBackground');
      Route::post('addMenuPhoto', 'FilesController@addMenuPhoto');
      Route::post('addMapLogo', 'FilesController@addMapLogo');
  });
});

Route::group(array('namespace' => 'Coupons', 'prefix' => 'coupons'), function() {
    Route::post('changeCoupon', 'CouponsController@changeCoupon'); // checked MSC
    Route::post('removeCoupon', 'CouponsController@removeCoupon'); // checked MSC

    Route::post('addCouponToFavourite', 'CouponsController@addCouponToFavourite'); // checked MSC
    Route::post('removeFromFavourite', 'CouponsController@removeFromFavourite'); // checked MSC

    Route::get('getList/{id_local_data_main}', 'CouponsController@getList'); // checked MSC
    Route::get('getCouponsByCity/{id_city_const_type}', 'CouponsController@getCouponsByCity'); // checked MSC

    Route::get('getDetails/{id_coupon_data_main}', 'CouponsController@getDetails'); // checked MSC
    Route::get('getFavouriteList', 'CouponsController@getFavouriteList'); // checked MSC

    Route::post('orderCoupon', 'CouponsController@orderCoupon'); // checked MSC
    Route::post('checkCoupon', 'CouponsController@checkCoupon'); // checked MSC

    Route::get('getDetailsEdit/{id_coupon_data_main}', 'CouponsController@getDetailsEdit'); // checked MSC

    Route::get('getSupportCouponsByCity/{id_city_const_type}', 'CouponsController@getSupportCouponsByCity'); // checked MSC

    Route::group(array('prefix' => 'files'), function() {
      Route::post('addLogo', 'FilesController@addLogo');
  });
});

Route::group(array('namespace' => 'Tools', 'prefix' => 'tools'), function() {
    Route::post('getList', 'SelectItemController@getList');
});

Route::group(array('namespace' => 'Tags', 'prefix' => 'tags'), function() {
    Route::post('changeTag', 'TagsController@changeTag');

    Route::get('getList', 'TagsController@getList'); // checked DFZ
});

Route::group(array('namespace' => 'Manager', 'prefix' => 'manager'), function() {
    Route::post('registerWorker', 'ManagerController@registerWorker');
    Route::get('getLocalsByManager', 'ManagerController@getLocalsByManager'); // checked MSC
    Route::get('getLocalStatistics/{id_local_data_main}', 'ManagerController@getLocalStatistics'); // checked MSC
    Route::get('getWorkerList/{id_local_data_main}', 'ManagerController@getWorkerList'); // checked MSC
});

