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

Route::get('/token', function () {
        echo csrf_token(); 

  });


Route::post('/getList', 'ListController@getList');


Route::group(array('namespace' => 'Locals', 'prefix' => 'locals'), function() { 
  Route::post('addOpenDays', 'LocalsController@addOpenDays'); // checked MSC
  Route::post('addTagsToLocal', 'LocalsController@addTagsToLocal'); // checked MSC

  Route::get('getList', 'LocalsController@getList'); // checked MSC
  Route::get('getDetails/{id_local_data_main}', 'LocalsController@getDetails'); // checked MSC
});

Route::group(array('namespace' => 'Coupons', 'prefix' => 'coupons'), function() { 
  Route::post('addCoupon', 'CouponsController@addCoupon'); // checked MSC

  Route::get('getList/{id_local_data_main}', 'CouponsController@getList'); // checked MSC
  Route::get('getDetails/{id_local_data_main}/{id_coupon_data_main}', 'CouponsController@getDetails'); // checked MSC
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
