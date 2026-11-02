<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = ['genre_id', 'title', 'description', 'duration_minutes', 'age_rating', 'poster_path'];

    public function genre(): BelongsTo { return $this->belongsTo(Genre::class); }
    public function showtimes(): HasMany { return $this->hasMany(Showtime::class); }
    public function getPosterUrlAttribute(): string { return $this->poster_path ?: 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=800&q=80'; }
}
