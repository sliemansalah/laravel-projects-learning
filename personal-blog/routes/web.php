<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    return redirect()->route('posts.index');
});

Route::resource('posts', PostController::class);

Route::post('posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::get('comments/pending', [CommentController::class, 'pending'])->name('comments.pending');
Route::post('comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
Route::delete('comments/{comment}/reject', [CommentController::class, 'reject'])->name('comments.reject');

Route::get('archive', [PostController::class, 'archive'])->name('posts.archive');
Route::get('archive/{year}/{month}', [PostController::class, 'byDate'])->name('posts.by-date');

Route::post('posts/{post}/bookmark', [PostController::class, 'toggleBookmark'])->name('posts.bookmark');
Route::get('bookmarks', [PostController::class, 'bookmarks'])->name('posts.bookmarks');

Route::get('statistics', [PostController::class, 'statistics'])->name('posts.statistics');
