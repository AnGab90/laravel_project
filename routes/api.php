<?php

use App\Http\Controllers\PassportAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;


Route::post('register', [PassportAuthController::class, 'register'])->name('auth.register');
Route::post('login', [PassportAuthController::class, 'login'])->name('auth.login');
Route::post('verify', [PassportAuthController::class, 'verify'])->name('auth.verify');

Route::middleware('auth:api')->group(function () {
    Route::get('me', [PassportAuthController::class, 'me'])->name('auth.me');
    Route::get('logout', [PassportAuthController::class, 'logout'])->name('auth.logout');
    Route::get('users', [PassportAuthController::class, 'users'])->middleware('admin');
    Route::put('block/{id}', [PassportAuthController::class, 'block']);
    Route::put('updateUser/{id}', [PassportAuthController::class, 'updateUser'])->name('auth.update');
    Route::get('one-user/{id}', [PassportAuthController::class, 'oneUser']);

    Route::get('index', [PostController::class, 'index'])->middleware('admin');
    Route::get('postUser/{id}', [PostController::class, 'show']);
    Route::post('update/{id}', [PostController::class, 'update']);
    Route::delete('delete/{id}', [PostController::class, 'destroy']);
    Route::get('one-user-post/{id}', [PostController::class, 'oneMany']);
    Route::get('my-posts', [PostController::class, 'posts']);
    Route::post('new-post', [PostController::class, 'store']);

});


