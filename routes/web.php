<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GenreController as AdminGenreController;
use App\Http\Controllers\Admin\HallController as AdminHallController;
use App\Http\Controllers\Admin\MovieController as AdminMovieController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\ShowtimeController as AdminShowtimeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout.get');

Route::middleware('auth')->group(function () {
    Route::get('/book/{showtime}', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/book/{showtime}', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('movies', AdminMovieController::class)->except('show');
    Route::get('genres', [AdminGenreController::class, 'index'])->name('genres.index');
    Route::post('genres', [AdminGenreController::class, 'store'])->name('genres.store');
    Route::put('genres/{genre}', [AdminGenreController::class, 'update'])->name('genres.update');
    Route::delete('genres/{genre}', [AdminGenreController::class, 'destroy'])->name('genres.destroy');
    Route::get('halls', [AdminHallController::class, 'index'])->name('halls.index');
    Route::post('halls', [AdminHallController::class, 'store'])->name('halls.store');
    Route::put('halls/{hall}', [AdminHallController::class, 'update'])->name('halls.update');
    Route::delete('halls/{hall}', [AdminHallController::class, 'destroy'])->name('halls.destroy');
    Route::get('showtimes', [AdminShowtimeController::class, 'index'])->name('showtimes.index');
    Route::post('showtimes', [AdminShowtimeController::class, 'store'])->name('showtimes.store');
    Route::delete('showtimes/{showtime}', [AdminShowtimeController::class, 'destroy'])->name('showtimes.destroy');
    Route::get('reservations', [AdminReservationController::class, 'index'])->name('reservations.index');
    Route::post('reservations/{reservation}/cancel', [AdminReservationController::class, 'cancel'])->name('reservations.cancel');
});
