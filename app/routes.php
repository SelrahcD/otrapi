<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

Route::filter('api.auth', 'ApiAuthFilter');

Route::post('/auth', 'AuthController@getToken');
Route::post('/users', 'UsersController@create');

Route::group(array('before' => 'api.auth'), function()
{
	Route::get('/me', 'UsersController@showMe');
	Route::put('/me', 'UsersController@editMe');
});
