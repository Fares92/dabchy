<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
/**
 * facebook
 */
Route::get('/redirect', 'SocialAuthController@redirect');
Route::get('/callback', 'SocialAuthController@callback');
/**
 * instagram redirection
 */
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
Route::get('/redirect_insta', 'SocialAuthController@redirect_insta');
Route::get('/callback_insta', 'SocialAuthController@callback_insta');
Route::get('getToken','SocialAuthController@getToken');

Route::post('importExcel', 'ExcelController@importExcel');

Route::post('/forget_password', 'EmailController@forgetPassword');