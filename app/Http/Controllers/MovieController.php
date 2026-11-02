<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $movies = Movie::with('genre')->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->q.'%'))->when($request->filled('genre'), fn ($q) => $q->where('genre_id', $request->genre))->latest()->paginate(12)->withQueryString();
        return view('movies.index', ['movies' => $movies, 'genres' => Genre::orderBy('name')->get()]);
    }

    public function show(Movie $movie)
    {
        $movie->load(['genre', 'showtimes' => fn ($q) => $q->with('hall')->where('show_date', '>=', today())->orderBy('show_date')->orderBy('start_time')]);
        return view('movies.show', compact('movie'));
    }
}
