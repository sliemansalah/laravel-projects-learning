<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuranController;
use App\Http\Controllers\Api\MemorizationController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Quran data (public access)
Route::prefix('quran')->group(function () {
    Route::get('/surahs', [QuranController::class, 'getSurahs']);
    Route::get('/surahs/{surahId}', [QuranController::class, 'getSurah']);
    Route::get('/surahs/{surahId}/ayahs', [QuranController::class, 'getSurahAyahs']);
    Route::get('/ayahs/{ayahId}', [QuranController::class, 'getAyah']);
    Route::get('/search', [QuranController::class, 'search']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // Memorization
    Route::prefix('memorization')->group(function () {
        Route::post('/start', [MemorizationController::class, 'startAyah']);
        Route::post('/submit-word', [MemorizationController::class, 'submitWord']);
        Route::get('/today-review', [MemorizationController::class, 'getTodayReview']);
        Route::get('/progress', [MemorizationController::class, 'getProgress']);
        Route::post('/complete-session', [MemorizationController::class, 'completeSession']);
    });

    // User
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::patch('/settings', [UserController::class, 'updateSettings']);
        Route::get('/stats', [UserController::class, 'getStats']);
        Route::get('/achievements', [UserController::class, 'getAchievements']);
    });
});
