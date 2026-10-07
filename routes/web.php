<?php

use Illuminate\Support\Facades\Route;

// Semua route di-handle oleh Vue SPA, kecuali /api
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*')->name('login');