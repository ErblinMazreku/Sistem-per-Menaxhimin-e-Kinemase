<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request) { return view('reservations.index', ['reservations' => $request->user()->reservations()->with(['showtime.movie', 'showtime.hall', 'seats'])->latest()->paginate(10)]); }
    public function show(Request $request, Reservation $reservation) { abort_unless($reservation->user_id === $request->user()->id, 403); return view('reservations.show', compact('reservation')); }
    public function cancel(Request $request, Reservation $reservation) { abort_unless($reservation->user_id === $request->user()->id, 403); abort_if($reservation->status !== 'active', 422, 'This reservation is already cancelled.'); $reservation->cancel(); return back()->with('success', 'Reservation cancelled and seats released.'); }
}
