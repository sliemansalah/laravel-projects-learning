<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');
Route::resource('posts', PostController::class)->except(['index']);
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
