<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
 */

Route::get('/auth/user', 'AuthController@user');
Route::post('/auth/user', 'AuthController@register');

Route::put('/profile/avatar', 'ProfileController@updateAvatar');
Route::put('/profile/update', 'ProfileController@updateInfo');
Route::get('/profile/{user}', 'PostsController@profile');

Route::get('/notifications', 'NotificationsController@index');
Route::delete('/notifications', 'NotificationsController@destroy');

Route::post('/posts/new/text', 'TextPostsController@store');
Route::post('/posts/new/photo', 'PhotoPostsController@store');
Route::get('/posts/{post}', 'PostsController@show');
Route::delete('/posts/{post}', 'PostsController@destroy');
Route::post('/posts/{post}/reactions', 'ReactionsController@store');
Route::get('/posts/{post}/comments', 'CommentsController@index');
Route::post('/posts/{post}/comments', 'CommentsController@store');

Route::put('/comments/{comment}', 'CommentsController@update');
Route::delete('/comments/{comment}', 'CommentsController@destroy');

Route::get('/timeline', 'PostsController@timeline');

Route::get('/friendships', 'FriendshipsController@index');
Route::post('/friendships/add/{recipient}', 'FriendshipsController@addFriend');
Route::post('/friendships/accept/{sender}', 'FriendshipsController@acceptFriendship');
Route::post('/friendships/deny/{sender}', 'FriendshipsController@denyFriendship');
Route::post('/friendships/unfriend/{friend}', 'FriendshipsController@unfriend');
