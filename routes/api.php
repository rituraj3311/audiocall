<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'signin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('calls')->group(function () {
        Route::post('/start', [UserController::class, 'StartCall']);
        Route::post('/respond', [UserController::class, 'ResponseCall']);
        Route::post('/end', [UserController::class, 'EndCall']);
    });
    Route::get('/users', [UserController::class, 'UserList']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
