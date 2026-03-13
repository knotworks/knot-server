<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'This is an API, go away! >:-(';
});

Route::post('login', [AuthController::class, 'store'])->name('login');
Route::post('logout', [AuthController::class, 'destroy'])->middleware('auth:sanctum')->name('logout');
