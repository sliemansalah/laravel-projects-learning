<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/system-info', [App\Http\Controllers\SystemInfoController::class, 'index'])
        ->name('system-info');
    Route::get('/about', [App\Http\Controllers\AboutController::class, 'index'])
        ->name('about');

    // Routes الشركات
    Route::resource('companies', CompanyController::class);
    Route::post('companies/{company}/switch', [CompanyController::class, 'switchCompany'])
        ->name('companies.switch');
});
require __DIR__.'/auth.php';
