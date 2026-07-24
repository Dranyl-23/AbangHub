<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/auth/google', [AuthController::class, 'googleAuth']);

// Protected Routes (Requires Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Properties API
    Route::get('/properties', [\App\Http\Controllers\Api\PropertyController::class, 'index']);
    Route::post('/properties', [\App\Http\Controllers\Api\PropertyController::class, 'store']);
    Route::get('/properties/{id}', [\App\Http\Controllers\Api\PropertyController::class, 'show']);

    // Applications API
    Route::get('/applications', [\App\Http\Controllers\Api\ApplicationController::class, 'index']);
    Route::post('/properties/{id}/apply', [\App\Http\Controllers\Api\ApplicationController::class, 'store']);

    // Favorites / Saved Properties API
    Route::get('/favorites', [\App\Http\Controllers\Api\FavoriteController::class, 'index']);
    Route::post('/favorites/{id}', [\App\Http\Controllers\Api\FavoriteController::class, 'toggle']);

    // Maintenance API
    Route::get('/maintenance', [\App\Http\Controllers\Api\MaintenanceController::class, 'index']);
    Route::post('/maintenance', [\App\Http\Controllers\Api\MaintenanceController::class, 'store']);

    // Wallet API
    Route::get('/wallet', [\App\Http\Controllers\Api\WalletController::class, 'balance']);
    Route::get('/transactions', [\App\Http\Controllers\Api\WalletController::class, 'transactions']);

    // Profile API
    Route::put('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'update']);
    Route::post('/profile/avatar', [\App\Http\Controllers\Api\ProfileController::class, 'uploadAvatar']);
    Route::post('/profile/id', [\App\Http\Controllers\Api\ProfileController::class, 'uploadId']);
    Route::put('/profile/password', [\App\Http\Controllers\Api\ProfileController::class, 'changePassword']);
});
