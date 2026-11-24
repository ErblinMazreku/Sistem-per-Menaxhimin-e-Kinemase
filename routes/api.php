<?php

use App\Http\Controllers\TmdbController;
use Illuminate\Support\Facades\Route;

Route::get('/movies/popular', [TmdbController::class, 'popular'])->name('api.movies.popular');
Route::get('/movies/search', [TmdbController::class, 'search'])->name('api.movies.search');
Route::get('/movies/{movieId}', [TmdbController::class, 'show'])
    ->whereNumber('movieId')
    ->name('api.movies.show');
