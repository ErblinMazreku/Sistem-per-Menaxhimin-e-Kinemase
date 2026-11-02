<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    public function index() { return view('admin.showtimes.index', ['showtimes' => Showtime::with(['movie', 'hall'])->where('show_date', '>=', today())->orderBy('show_date')->orderBy('start_time')->paginate(15), 'movies' => Movie::orderBy('title')->get(), 'halls' => Hall::orderBy('name')->get()]); }
    public function store(Request $request) { $data = $request->validate(['movie_id' => ['required', 'exists:movies,id'], 'hall_id' => ['required', 'exists:halls,id'], 'show_date' => ['required', 'date'], 'start_time' => ['required'], 'ticket_price' => ['required', 'numeric', 'min:0']]); $movie = Movie::find($data['movie_id']); $start = strtotime($data['show_date'].' '.$data['start_time']); $end = $start + ($movie->duration_minutes + 20) * 60; $overlap = Showtime::where('hall_id', $data['hall_id'])->where('show_date', $data['show_date'])->with('movie')->get()->contains(fn ($s) => $start < strtotime($s->show_date->format('Y-m-d').' '.$s->start_time) + ($s->movie->duration_minutes + 20) * 60 && $end > strtotime($s->show_date->format('Y-m-d').' '.$s->start_time)); abort_if($overlap, 422, 'That hall already has an overlapping screening.'); Showtime::create($data); return back()->with('success', 'Showtime scheduled.'); }
    public function destroy(Showtime $showtime) { abort_if($showtime->reservations()->where('status', 'active')->exists(), 422, 'Cannot remove a showtime with active reservations.'); $showtime->delete(); return back()->with('success', 'Showtime removed.'); }
}
