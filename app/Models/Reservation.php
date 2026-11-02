<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'showtime_id', 'status', 'booking_reference'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function showtime(): BelongsTo { return $this->belongsTo(Showtime::class); }
    public function seats(): BelongsToMany { return $this->belongsToMany(Seat::class, 'reservation_seats')->withPivot(['showtime_id', 'reserved_at'])->withTimestamps(); }
    public function cancel(): void { $this->update(['status' => 'cancelled']); ActiveSeatAllocation::where('reservation_id', $this->id)->delete(); }
}
