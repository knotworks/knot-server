<?php

declare(strict_types=1);

use App\Http\Controllers\CommentsController;
use App\Http\Controllers\FriendshipsController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PasswordResetsController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReactionsController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\TokensController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('tokens/create', [TokensController::class, 'store'])->name('tokens.create');
Route::post('register', [UserController::class, 'store'])->name('register');

Route::post('forgot-password', [PasswordResetsController::class, 'store'])->name('password.forgot');
Route::get('reset-password/{token}', [PasswordResetsController::class, 'show'])->name('password.reset');
Route::post('reset-password', [PasswordResetsController::class, 'update'])->name('password.update');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', [UserController::class, 'show'])->name('user');

    Route::put('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::put('/profile/update', [ProfileController::class, 'updateInfo'])->name('profile.update');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/media/posts/{postMedia}', [MediaController::class, 'showPostMedia'])->name('media.posts.show');
    Route::get('/media/avatars/{user}', [MediaController::class, 'showAvatar'])->name('media.avatars.show');

    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::delete('/notifications', [NotificationsController::class, 'destroy'])->name('notifications.destroy');

    Route::post('/posts', [PostsController::class, 'store']);
    Route::get('/posts/{post}', [PostsController::class, 'show']);
    Route::delete('/posts/{post}', [PostsController::class, 'destroy']);

    Route::post('/posts/{post}/reactions', [ReactionsController::class, 'store']);

    Route::get('/posts/{post}/comments', [CommentsController::class, 'index']);
    Route::post('/posts/{post}/comments', [CommentsController::class, 'store']);
    Route::put('/comments/{comment}', [CommentsController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentsController::class, 'destroy']);

    Route::get('/timeline', [TimelineController::class, 'show']);

    Route::get('/friendships', [FriendshipsController::class, 'index']);
    Route::post('/friendships/add/{recipient}', [FriendshipsController::class, 'addFriend']);
    Route::post('/friendships/accept/{sender}', [FriendshipsController::class, 'acceptFriendship']);
    Route::post('/friendships/deny/{sender}', [FriendshipsController::class, 'denyFriendship']);
    Route::post('/friendships/unfriend/{friend}', [FriendshipsController::class, 'unfriend']);

    Route::post('/services/nearby', [ServicesController::class, 'fetchNearby']);
    Route::post('/services/current-location', [ServicesController::class, 'fetchCurrentLocation']);
    Route::post('/services/link-meta', [ServicesController::class, 'fetchLinkMeta']);
});
