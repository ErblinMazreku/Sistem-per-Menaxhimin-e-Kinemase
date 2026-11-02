<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Reservation;
use App\Models\Showtime;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', ['stats' => ['movies' => Movie::count(), 'genres' => Genre::count(), 'halls' => Hall::count(), 'showtimes' => Showtime::where('show_date', '>=', today())->count(), 'reservations' => Reservation::count(), 'users' => \App\Models\User::count()], 'recent' => Reservation::with(['user', 'showtime.movie'])->latest()->take(8)->get()]);
    }
}
