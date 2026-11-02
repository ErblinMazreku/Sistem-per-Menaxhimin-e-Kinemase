<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index(Request $request) { return view('admin.movies.index', ['movies' => Movie::with('genre')->when($request->q, fn ($q) => $q->where('title', 'like', '%'.$request->q.'%'))->latest()->paginate(15), 'genres' => Genre::orderBy('name')->get()]); }
    public function create() { return view('admin.movies.form', ['movie' => new Movie, 'genres' => Genre::orderBy('name')->get()]); }
    public function store(Request $request) { $movie = Movie::create($this->data($request)); return redirect()->route('admin.movies.index')->with('success', $movie->title.' added to the catalogue.'); }
    public function edit(Movie $movie) { return view('admin.movies.form', ['movie' => $movie, 'genres' => Genre::orderBy('name')->get()]); }
    public function update(Request $request, Movie $movie) { $movie->update($this->data($request)); return redirect()->route('admin.movies.index')->with('success', 'Movie updated.'); }
    public function destroy(Movie $movie) { abort_if($movie->showtimes()->exists(), 422, 'Remove its showtimes before deleting this movie.'); $movie->delete(); return back()->with('success', 'Movie removed.'); }
    private function data(Request $request): array { return $request->validate(['genre_id' => ['required', 'exists:genres,id'], 'title' => ['required', 'max:255'], 'description' => ['required'], 'duration_minutes' => ['required', 'integer', 'min:1', 'max:500'], 'age_rating' => ['required', 'max:10'], 'poster_path' => ['nullable', 'url', 'max:1000']]); }
}
