<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveSeatAllocation extends Model
{
    protected $fillable = ['showtime_id', 'seat_id', 'reservation_id'];
}
