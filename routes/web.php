<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\PublicController::class, 'index'])->name('landing');
Route::get('/instansi/{slug}', [\App\Http\Controllers\PublicController::class, 'show'])->name('instansi.show');
