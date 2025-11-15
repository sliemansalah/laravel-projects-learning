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

Route::post('posts/{post}/bookmark', [PostController::class, 'toggleBookmark'])->name('posts.bookmark');
Route::get('bookmarks', [PostController::class, 'bookmarks'])->name('posts.bookmarks');

Route::get('statistics', [PostController::class, 'statistics'])->name('posts.statistics');
