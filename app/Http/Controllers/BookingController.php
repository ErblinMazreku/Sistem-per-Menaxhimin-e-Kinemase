<?php

namespace App\Http\Controllers;

use App\Models\ActiveSeatAllocation;
use App\Models\Reservation;
use App\Models\Seat;
use App\Models\Showtime;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(Showtime $showtime)
    {
        $showtime->load(['movie', 'hall.seats']);
        $occupied = ActiveSeatAllocation::where('showtime_id', $showtime->id)->pluck('seat_id')->all();
        return view('bookings.create', compact('showtime', 'occupied'));
    }

    public function store(Request $request, Showtime $showtime)
    {
        $data = $request->validate(['seats' => ['required', 'array', 'min:1'], 'seats.*' => ['integer', 'distinct', 'exists:seats,id']]);
        try {
            $reservation = DB::transaction(function () use ($data, $showtime, $request) {
                $seatIds = $data['seats'];
                $valid = Seat::where('hall_id', $showtime->hall_id)->whereIn('id', $seatIds)->pluck('id')->all();
                abort_if(count($valid) !== count($seatIds), 422, 'One or more selected seats are not in this hall.');
                $reservation = Reservation::create(['user_id' => $request->user()->id, 'showtime_id' => $showtime->id, 'booking_reference' => 'CIN-'.strtoupper(Str::random(8))]);
                $reservation->seats()->attach($seatIds, ['showtime_id' => $showtime->id, 'reserved_at' => now()]);
                foreach ($seatIds as $seatId) ActiveSeatAllocation::create(['showtime_id' => $showtime->id, 'seat_id' => $seatId, 'reservation_id' => $reservation->id]);
                return $reservation;
            });
        } catch (QueryException $e) {
            return back()->withErrors(['seats' => 'One of those seats was just booked. Please choose again.']);
        }
        return redirect()->route('reservations.show', $reservation)->with('success', 'Your seats are confirmed.');
    }
}
