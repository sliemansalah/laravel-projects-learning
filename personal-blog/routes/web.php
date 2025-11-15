<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    return redirect()->route('posts.index');
});

Route::resource('posts', PostController::class);

Route::post('posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

Route::get('archive', [PostController::class, 'archive'])->name('posts.archive');
Route::get('archive/{year}/{month}', [PostController::class, 'byDate'])->name('posts.by-date');
