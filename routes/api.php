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
Route::get('/', function(){
	return view('welcome');});
Route::get('/v1/main', 'HomeController@getMain');
Route::get('/v1/main/categories','HomeController@getCategoriesList');
Route::get('/v1/main/categories/{categoryCode}','GoodsController@getGoodsList');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//Route::get('/test','TestController@index');
