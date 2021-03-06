<?php

use Illuminate\Http\Request;
use Auth;

Route::get('/user', function () {
    $current_user = Auth::user();
    return $current_user;
})->middleware('auth:api');

Route::group(array('namespace' => 'Locals', 'prefix' => 'locals'), function () {
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
    Route::get('getOrderedList/{id_city_const_type}/{id_sort_const_type}', 'LocalsController@getOrderedList'); // checked DFZ
    Route::get('getOrderedFavouriteList/{id_city_const_type}/{id_sort_const_type}', 'LocalsController@getOrderedFavouriteList'); // checked DFZ

    Route::get('getDetailsEdit/{id_local_data_main}', 'LocalsController@getDetailsEdit'); // checked MSC

    Route::post('showFacebookCount', 'LocalsStatisticsController@showFacebookCount'); // checked MSC
    Route::post('showInstagramCount', 'LocalsStatisticsController@showInstagramCount'); // checked MSC
    Route::post('showMenuCount', 'LocalsStatisticsController@showMenuCount'); // checked MSC
    Route::post('showPhonenumberCount', 'LocalsStatisticsController@showPhonenumberCount'); // checked MSC
    Route::post('showOrderCount', 'LocalsStatisticsController@showOrderCount'); // checked DFZ

    Route::group(array('prefix' => 'files'), function () {
        Route::post('addLogo', 'FilesController@addLogo'); // checked MSC
        Route::post('addBackground', 'FilesController@addBackground'); // checked MSC
        Route::post('addMenuPhoto', 'FilesController@addMenuPhoto'); // checked MSC
        Route::post('addMapLogo', 'FilesController@addMapLogo'); // checked MSC
        Route::post('addMenuPhotos', 'FilesController@addMenuPhotos'); // checked MSC
        Route::post('countMenuPhotos', 'FilesController@countMenuPhotos'); // checked MSC
    });
});

Route::group(array('namespace' => 'Coupons', 'prefix' => 'coupons'), function () {
    Route::post('changeCoupon', 'CouponsController@changeCoupon'); // checked MSC
    Route::post('removeCoupon', 'CouponsController@removeCoupon'); // checked MSC

    Route::post('addCouponToFavourite', 'CouponsController@addCouponToFavourite'); // checked MSC
    Route::post('removeFromFavourite', 'CouponsController@removeFromFavourite'); // checked MSC

    Route::get('getList/{id_local_data_main}', 'CouponsController@getList'); // checked MSC
    Route::get('getCouponsByCity/{id_city_const_type}', 'CouponsController@getCouponsByCity'); // checked MSC
    Route::get('getOrderedListByCity/{id_city_const_type}/{id_sort_const_type}', 'CouponsController@getOrderedListByCity'); // checked DFZ
    Route::get('getOrderedFavouriteList/{id_sort_const_type}', 'CouponsController@getOrderedFavouriteList'); // checked DFZ

    Route::get('getDetails/{id_coupon_data_main}', 'CouponsController@getDetails'); // checked MSC
    Route::get('getFavouriteList', 'CouponsController@getFavouriteList'); // checked MSC

    Route::post('orderCoupon', 'CouponsController@orderCoupon'); // checked MSC
    Route::post('checkCoupon', 'CouponsController@checkCoupon'); // checked MSC

    //desktop
    Route::post('checkCouponDesktopApp', 'CouponsController@checkCouponDesktopApp'); // checked MSC
    Route::post('checkCouponNameDesktopApp', 'CouponsController@checkCouponNameDesktopApp'); // checked MSC
    Route::get('getUsedCouponsStatistic', 'CouponsController@getUsedCouponsStatistic'); // checked MSC


    Route::get('getDetailsEdit/{id_coupon_data_main}', 'CouponsController@getDetailsEdit'); // checked MSC

    Route::get('getSupportCouponsByCity/{id_city_const_type}', 'CouponsController@getSupportCouponsByCity'); // checked MSC

    Route::group(array('prefix' => 'files'), function () {
        Route::post('addLogo', 'FilesController@addLogo'); // checked MSC
    });
});

Route::group(array('namespace' => 'Tools', 'prefix' => 'tools'), function () {
    Route::post('getList', 'SelectItemController@getList'); // checked MSC
});

Route::group(array('namespace' => 'Tags', 'prefix' => 'tags'), function () {
    Route::post('changeTag', 'TagsController@changeTag'); // checked DFZ

    Route::get('getList', 'TagsController@getList'); // checked DFZ
});

Route::group(array('namespace' => 'Manager', 'prefix' => 'manager'), function () {
    Route::post('registerWorker', 'ManagerController@registerWorker'); // checked MSC
    Route::get('getLocalsByManager', 'ManagerController@getLocalsByManager'); // checked MSC
    Route::get('getLocalStatistics/{id_local_data_main}', 'ManagerController@getLocalStatistics'); // checked MSC
    Route::get('getWorkerList/{id_local_data_main}', 'ManagerController@getWorkerList'); // checked MSC
    Route::post('removeWorker', 'ManagerController@removeWorker'); // checked MSC
});

Route::group(array('namespace' => 'Feedback', 'prefix' => 'feedback'), function () {
    Route::post('add', 'FeedbackController@add'); // checked DFZ

});
