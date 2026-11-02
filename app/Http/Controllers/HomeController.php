<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'featured' => Movie::with('genre')->latest()->first(),
            'featuredMovies' => Movie::with('genre')->latest()->take(4)->get(),
            'movies' => Movie::with('genre')->latest()->take(6)->get(),
            'genres' => Genre::withCount('movies')->orderBy('name')->get(),
        ]);
    }
}
