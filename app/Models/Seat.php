<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seat extends Model
{
    protected $fillable = ['hall_id', 'row_label', 'seat_number'];
    public function hall(): BelongsTo { return $this->belongsTo(Hall::class); }
    public function label(): string { return $this->row_label.$this->seat_number; }
}
