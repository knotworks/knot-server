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

Route::get('/auth/user', 'AuthController@user');
Route::post('/auth/user', 'AuthController@register');

Route::get('/notifications', 'NotificationsController@index');
Route::delete('/notifications', 'NotificationsController@destroy');

Route::post('/posts/new/text', 'TextPostsController@store');
Route::post('/posts/new/photo', 'PhotoPostsController@store');
Route::post('/posts/{post}/reactions', 'ReactionsController@store');
Route::get('/posts/{post}/comments', 'CommentsController@index');
Route::post('/posts/{post}/comments', 'CommentsController@store');
Route::put('/comments/{comment}', 'CommentsController@update');
Route::delete('/comments/{comment}', 'CommentsController@destroy');

Route::get('/feed', 'PostsController@feed');

Route::get('/friendships', 'FriendshipsController@index');
Route::post('/friendships/add/{recipient}', 'FriendshipsController@addFriend');
Route::post('/friendships/accept/{sender}', 'FriendshipsController@acceptFriendship');
Route::post('/friendships/deny/{sender}', 'FriendshipsController@denyFriendship');
Route::post('/friendships/unfriend/{friend}', 'FriendshipsController@unfriend');
