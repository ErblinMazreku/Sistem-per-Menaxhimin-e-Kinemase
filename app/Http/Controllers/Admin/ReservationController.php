<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function index() { return view('admin.reservations.index', ['reservations' => Reservation::with(['user', 'showtime.movie', 'showtime.hall', 'seats'])->latest()->paginate(20)]); }
    public function cancel(Reservation $reservation) { abort_if($reservation->status !== 'active', 422, 'Reservation is already cancelled.'); $reservation->cancel(); return back()->with('success', 'Reservation cancelled.'); }
}
