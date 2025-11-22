<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SurahController;
use App\Http\Controllers\Api\AyahController;
use App\Http\Controllers\Api\HifzController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// =============== Routes للسور والآيات (بدون مصادقة) ===============
Route::prefix('quran')->group(function () {

    // السور
    Route::get('/surahs', [SurahController::class, 'index']);
    Route::get('/surah/{id}', [SurahController::class, 'show']);
    Route::get('/surah/number/{number}', [SurahController::class, 'getByNumber']);
    Route::get('/surah/{surahId}/ayahs', [SurahController::class, 'ayahs']);
    Route::get('/surahs/search', [SurahController::class, 'search']);

    // الآيات
    Route::get('/ayah/{id}', [AyahController::class, 'show']);
    Route::get('/page/{pageNumber}', [AyahController::class, 'getByPage']);
    Route::get('/juz/{juzNumber}', [AyahController::class, 'getByJuz']);
    Route::get('/ayahs/search', [AyahController::class, 'search']);
    Route::get('/surah/{surahId}/ayahs', [AyahController::class, 'getBySurah']);
});

// =============== Routes للحفظ (تحتاج مصادقة) ===============
Route::middleware('auth:sanctum')->prefix('hifz')->group(function () {

    // بدء وإدارة الحفظ
    Route::post('/start', [HifzController::class, 'startHifz']);
    Route::get('/user', [HifzController::class, 'getUserHifz']);
    Route::put('/{hifzId}/status', [HifzController::class, 'updateHifzStatus']);

    // إدارة الصفحات
    Route::post('/page/memorize', [HifzController::class, 'markPageMemorized']);
    Route::post('/page/review', [HifzController::class, 'reviewPage']);
    Route::get('/pages', [HifzController::class, 'getUserPages']);

    // الإحصائيات
    Route::get('/stats', [HifzController::class, 'getStats']);
});

// =============== Authentication Routes ===============
Route::post('/register', 'App\Http\Controllers\Api\AuthController@register');
Route::post('/login', 'App\Http\Controllers\Api\AuthController@login');
Route::post('/logout', 'App\Http\Controllers\Api\AuthController@logout')->middleware('auth:sanctum');
