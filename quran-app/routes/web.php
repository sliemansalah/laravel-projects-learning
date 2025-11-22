<?php

use Illuminate\Support\Facades\Route;

// جميع المسارات تُعاد إلى تطبيق Vue
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
