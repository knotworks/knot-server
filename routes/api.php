<?php

use Cloudinary\Api\ApiUtils;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
 */

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', 'UserController@show');

    Route::put('/profile/avatar', 'ProfileController@updateAvatar');
    Route::put('/profile/update', 'ProfileController@updateInfo');
    Route::get('/profile/{user}', 'ProfileController@show');

    Route::get('/notifications', 'NotificationsController@index');
    Route::delete('/notifications', 'NotificationsController@destroy');

    Route::post('/posts', 'PostsController@store');
    Route::get('/posts/{post}', 'PostsController@show');
    Route::delete('/posts/{post}', 'PostsController@destroy');

    Route::post('/posts/{post}/reactions', 'ReactionsController@store');

    Route::get('/posts/{post}/comments', 'CommentsController@index');
    Route::post('/posts/{post}/comments', 'CommentsController@store');
    Route::put('/comments/{comment}', 'CommentsController@update');
    Route::delete('/comments/{comment}', 'CommentsController@destroy');

    Route::get('/timeline', 'TimelineController@show');

    Route::get('/friendships', 'FriendshipsController@index');
    Route::post('/friendships/add/{recipient}', 'FriendshipsController@addFriend');
    Route::post('/friendships/accept/{sender}', 'FriendshipsController@acceptFriendship');
    Route::post('/friendships/deny/{sender}', 'FriendshipsController@denyFriendship');
    Route::post('/friendships/unfriend/{friend}', 'FriendshipsController@unfriend');

    Route::post('/generate-cloudinary-signature', function (Request $request) {
        return ApiUtils::signParameters(config('services.cloudinary.secret'), $request->only('timestamp', 'upload_preset'));
    });
});
