<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(\App\Http\Controllers\API\V1\AuthController::class)->group(function () {
    Route::post('/register', 'register')->name('auth.register');
    Route::post('/login', 'login')->name('auth.login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('chats')->controller(\App\Http\Controllers\API\V1\ChatController::class)->group(function () {
        Route::post('/', 'store')->name('chats.store');
        Route::put('/{chat}', 'update')->name('chats.update');
        Route::delete('/{chat}', 'destroy')->name('chats.destroy');
        Route::post('/{chat}/join', 'join')->name('chats.join');
    });

    Route::prefix('/chats/{chat}/members')->controller(\App\Http\Controllers\API\V1\ChatMemberController::class)->group(function () {
        Route::get('/', 'index')->name('chats.members.index');
        Route::delete('/{member}', 'destroy')->name('chats.members.destroy');
        Route::put('/{member}', 'update')->name('chats.members.update');
    });
});
