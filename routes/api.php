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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
/**********************************************************************************************/
/**
 * Client routes
 */
//Route::resource('user', 'UserController', ['only' => ['index','edit','update']]);
Route::post('u_authenticate', 'UserController@authenticate');
Route::post('u_sign_up', 'UserController@signUp');
Route::get('u_logout','UserController@logout');
header('Access-Control-Allow-Origin: http://localhost:8081');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
Route::post('u_attach_subInterests','UserController@assign_subInterests');
Route::post('u_edit_subInterests','UserController@edit_interests');
/***update-profile***/
Route::get('u_edit_profile','UserController@editProfile');
Route::post('u_update_profile','UserController@updateProfile');
Route::post('u_upload_photo','UserController@uploadPhoto');
header('Access-Control-Allow-Origin:  *');
Route::get('sub_interests','Sub_InterestController@getAll');
Route::get('my_interests','UserController@my_interests');
Route::get('get_salons_names','SalonController@getAll');

Route::post('change_password','UserController@changePassword');
Route::post('u_forget_password','UserController@forgetPassword');

/*****Discover ***********/
Route::get('/u_index','UserController@index');
Route::get('/u_search','UserController@search');
Route::get('/u_salon','UserController@get_salon');
Route::post('contact_us','UserController@contactUs');
Route::post('suggest_salon','UserController@suggestSalon');
Route::post('error_report','UserController@reportError');
Route::post('like_salon','UserController@likeSalon');
Route::post('dislike_salon','UserController@dislikeSalon');


/**********************************************************************************************/

/**
 * Salon routes
 */
Route::resource('salon', 'SalonController', ['only' => ['index']]);
header('Access-Control-Allow-Origin:   http://localhost:8081');
header('Access-Control-Allow-Methods: POST, GET,DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
Route::post('s_authenticate', 'SalonController@authenticate');
Route::get('s_logout','SalonController@logout');
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
Route::post('s_sign_up', 'SalonController@signUp');
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
Route::post('s_create_profile', 'SalonController@create_Profile');
Route::post('s_add_address','SalonController@add_address');
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
Route::post('s_upload_photo', 'SalonController@upload_Photo');
Route::post('s_upload_menu_photos', 'SalonController@attach_menu_photos');
Route::post('s_upload_business_photos', 'SalonController@attach_business_photos');
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
Route::post('s_complete_profile', 'SalonController@complete_Profile');
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
Route::post('s_add_service', 'SalonController@add_Service');
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
Route::get('get_services','SalonController@get_services');
Route::get('get_days','SalonController@get_days');

Route::get('s_edit_profile','SalonController@editProfile');
Route::post('s_update_profile','SalonController@updateProfile');
Route::delete('s_delete_bPhoto/{id}','SalonController@delete_business_photo');
Route::delete('s_delete_mPhoto/{id}','SalonController@delete_menu_photo');

Route::post('s_attach_days','SalonController@attach_days');
Route::post('s_attach_facilities','SalonController@attach_facilities');
Route::post('s_attach_partnerShips','SalonController@attach_partnerShips');

Route::delete('s_detach_service/{id_serv}','SalonController@detach_service');
Route::delete('s_detach_partnerShip/{id_p}','SalonController@detach_partnership');
Route::post('s_change_settings','SalonController@change_settings');
Route::get('get_followers','SalonController@salonFollowers');


/**********************************************************************************************/

/**
 * Admin routes
 */
Route::post('a_authenticate', 'AdminController@authenticate');
Route::get('a_logout','AdminController@logout');

header('Access-Control-Allow-Origin:   http://localhost:8081');
header('Access-Control-Allow-Methods: POST, GET,DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::get('a_get_salons','AdminController@getSalons');
header('Access-Control-Allow-Origin:   http://localhost:8081');
header('Access-Control-Allow-Methods: POST, GET,DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::get('a_s_edit_profile/{id_s}','AdminController@editProfile');
header('Access-Control-Allow-Origin:   http://localhost:8081');
header('Access-Control-Allow-Methods: POST, GET,DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::post('a_s_update_profile/{id_s}','AdminController@updateProfile');
header('Access-Control-Allow-Origin:   http://localhost:8081');
header('Access-Control-Allow-Methods: POST, GET,DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::post('a_s_attach_days/{id_s}','AdminController@attach_days');
header('Access-Control-Allow-Origin:   http://localhost:8081');
header('Access-Control-Allow-Methods: POST, GET,DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::post('a_s_attach_facilities/{id_s}','AdminController@attach_facilities');
header('Access-Control-Allow-Origin:   http://localhost:8081');
header('Access-Control-Allow-Methods: POST, GET,DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::post('a_s_attach_partnerShips/{id_s}','AdminController@attach_partnerShips');
header('Access-Control-Allow-Origin:   http://localhost:8081');
header('Access-Control-Allow-Methods: POST, GET,DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::post('a_s_add_service/{id_s}', 'AdminController@add_service');
header('Access-Control-Allow-Origin:   http://localhost:8081');
header('Access-Control-Allow-Methods: POST, GET,DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::post('a_s_add_advertisement/{id_s}','AdminController@add_advertisement');
header('Access-Control-Allow-Origin:   http://localhost:8081');
header('Access-Control-Allow-Methods: POST, GET,DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::get('a_s_get_advertisement/{id_s}','AdminController@get_advertisement');
header('Access-Control-Allow-Origin:   http://localhost:8081');
header('Access-Control-Allow-Methods: POST, GET,DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::delete('a_s_delete_partnerShip/{id_p}','AdminController@detach_partnership');
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::delete('a_s_delete_advertisement/{id_ad}','AdminController@delete_advertisement');
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');


Route::delete('a_s_detach_service/{id_serv}','AdminController@detach_service');
header('Access-Control-Allow-Origin:   http://localhost:8081');
header('Access-Control-Allow-Methods: POST, GET,DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::get('a_get_clients','AdminController@getClients');
header('Access-Control-Allow-Origin:   http://localhost:8081');
header('Access-Control-Allow-Methods: POST, GET,DELETE, OPTIONS');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::post('a_s_add_address/{id_s}','AdminController@add_address');
Route::post('a_change_password','AdminController@change_password');
Route::post('a_s_attach_business_photos/{id_d}','AdminController@attach_business_photos');
Route::post('a_s_attach_menu_photos/{id_s}','AdminController@attach_menu_photos');
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');
Route::post('a_s_upload_photo/{id_s}','AdminController@upload_photo');
Route::get('a_s_get_services/{id_s}','AdminController@get_services');







/**
 * Beautician routes
 */
Route::resource('beautician', 'BeauticianController', ['only' => ['index']]);
Route::post('b_authenticate', 'BeauticianController@authenticate');
Route::post('b_sign_up', 'BeauticianController@signUp');


Route::post('getArticlesByPosition','UserController@getArticleFromPosition');