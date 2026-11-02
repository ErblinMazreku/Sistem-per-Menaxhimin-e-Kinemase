<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use Illuminate\Http\Request;

class HallController extends Controller
{
    public function index() { return view('admin.halls.index', ['halls' => Hall::withCount('seats')->withCount('showtimes')->orderBy('name')->get()]); }
    public function store(Request $request) { $data = $request->validate(['name' => ['required', 'max:100', 'unique:halls,name'], 'capacity' => ['required', 'integer', 'min:1', 'max:500']]); $hall = Hall::create($data); $this->seats($hall, $data['capacity']); return back()->with('success', 'Hall created with '.$data['capacity'].' seats.'); }
    public function update(Request $request, Hall $hall) { $data = $request->validate(['name' => ['required', 'max:100', 'unique:halls,name,'.$hall->id], 'capacity' => ['required', 'integer', 'min:1', 'max:500']]); abort_if($hall->showtimes()->whereHas('reservations', fn ($q) => $q->where('status', 'active'))->exists(), 422, 'A hall with active reservations cannot be resized.'); $hall->update($data); if ($hall->seats()->count() !== (int) $data['capacity']) { $hall->seats()->delete(); $this->seats($hall, $data['capacity']); } return back()->with('success', 'Hall updated.'); }
    public function destroy(Hall $hall) { abort_if($hall->showtimes()->exists(), 422, 'Remove showtimes before deleting this hall.'); $hall->delete(); return back()->with('success', 'Hall removed.'); }
    private function seats(Hall $hall, int $capacity): void { for ($i = 0; $i < $capacity; $i++) $hall->seats()->create(['row_label' => chr(65 + intdiv($i, 10)), 'seat_number' => ($i % 10) + 1]); }
}
