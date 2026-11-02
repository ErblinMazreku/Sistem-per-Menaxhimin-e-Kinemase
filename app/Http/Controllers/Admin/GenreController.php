<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class GenreController extends Controller
{
    public function index() { return view('admin.genres.index', ['genres' => Genre::withCount('movies')->orderBy('name')->get()]); }
    public function store(Request $request) { $request->validate(['name' => ['required', 'max:100', 'unique:genres,name']]); Genre::create($request->only('name')); return back()->with('success', 'Genre created.'); }
    public function update(Request $request, Genre $genre) { $request->validate(['name' => ['required', 'max:100', 'unique:genres,name,'.$genre->id]]); $genre->update($request->only('name')); return back()->with('success', 'Genre updated.'); }
    public function destroy(Genre $genre) { try { $genre->delete(); } catch (QueryException $e) { return back()->withErrors(['genre' => 'This genre still has movies assigned.']); } return back()->with('success', 'Genre removed.'); }
}
