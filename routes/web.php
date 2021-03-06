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


Route::get('/', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('email-verified', function(){ return view('home'); });

Auth::routes();

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');

Route::group(array('namespace' => 'AuthApi', 'prefix' => 'auth-api'), function() {
    Route::post('register', 'AuthApiController@register');
    Route::post('registerUuid', 'AuthApiController@registerUuid');
    Route::post('getUserStatus', 'AuthApiController@getUserStatus');
    Route::post('registerEmail', 'AuthApiController@registerEmail');
});
