<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
 */
Route::post('tokens/create', 'TokensController@store')->name('tokens.create');
Route::post('register', 'UserController@store')->name('register');

Route::post('forgot-password', 'PasswordResetsController@store')->name('password.forgot');
Route::get('reset-password/{token}', 'PasswordResetsController@show')->name('password.reset');
Route::post('reset-password', 'PasswordResetsController@update')->name('password.update');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', 'UserController@show')->name('user');

    Route::put('/profile/avatar', 'ProfileController@updateAvatar')->name('profile.avatar');
    Route::put('/profile/update', 'ProfileController@updateInfo')->name('profile.update');
    Route::get('/profile/{user}', 'ProfileController@show')->name('profile.show');

    Route::get('/notifications', 'NotificationsController@index')->name('notifications.index');
    Route::delete('/notifications', 'NotificationsController@destroy')->name('notifications.destroy');

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

    Route::post('/services/nearby', 'ServicesController@fetchNearby');

    Route::post('/services/current-location', 'ServicesController@fetchCurrentLocation');

    Route::post('/services/link-meta', 'ServicesController@fetchLinkMeta');

    Route::post('/services/generate-cloudinary-signature', 'ServicesController@generateCloudinarySignature');
});
