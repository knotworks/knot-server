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
    return 'This is an API, go away! >:-(';
});

Route::post('login', 'AuthController@store');
Route::post('register', 'UserController@store');
Route::post('logout', 'AuthController@destroy');
Route::post('forgot-password', 'PasswordResetsController@store');
Route::get('reset-password/{token}', 'PasswordResetsController@show')->name('password.reset');
Route::post('reset-password', 'PasswordResetsController@update')->name('password.update');
