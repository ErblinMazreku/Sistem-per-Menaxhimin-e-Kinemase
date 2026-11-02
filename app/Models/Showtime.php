<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Showtime extends Model
{
    use HasFactory;
    protected $fillable = ['movie_id', 'hall_id', 'show_date', 'start_time', 'ticket_price'];
    protected function casts(): array { return ['show_date' => 'date', 'ticket_price' => 'decimal:2']; }
    public function movie(): BelongsTo { return $this->belongsTo(Movie::class); }
    public function hall(): BelongsTo { return $this->belongsTo(Hall::class); }
    public function reservations(): HasMany { return $this->hasMany(Reservation::class); }
    public function reservedSeatIds(): array { return $this->reservations()->where('status', 'active')->with('seats')->get()->flatMap(fn ($r) => $r->seats->pluck('id'))->all(); }
}
