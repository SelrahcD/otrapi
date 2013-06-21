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

/* Authentication */
Route::post('/auth', 'AuthController@getToken');
Route::post('/auth/refresh', 'AuthController@refreshToken');

/* User creation */
Route::post('/users', 'UsersController@create');

Route::group(array('before' => 'api.auth'), function()
{
	/* User */
	Route::get('/me', 'UsersController@showMe');
	Route::put('/me', 'UsersController@editMe');

	/* Band */
	Route::post('/bands', 'BandsController@create');
	Route::get('/bands/{id}', 'BandsController@show');
	Route::get('/bands/{id}/members', 'BandsController@showMembers');
	Route::post('/bands/{id}/members', 'BandsController@addMember');
});
