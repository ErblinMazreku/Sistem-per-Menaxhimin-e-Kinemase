<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = ['genre_id', 'title', 'description', 'duration_minutes', 'age_rating', 'poster_path', 'trailer_url'];

    public function genre(): BelongsTo { return $this->belongsTo(Genre::class); }
    public function showtimes(): HasMany { return $this->hasMany(Showtime::class); }
    public function getPosterUrlAttribute(): string { return $this->poster_path ?: 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=800&q=80'; }

    public function getTrailerEmbedUrlAttribute(): ?string
    {
        if (! $this->trailer_url || parse_url($this->trailer_url, PHP_URL_SCHEME) !== 'https') {
            return null;
        }

        $host = strtolower((string) parse_url($this->trailer_url, PHP_URL_HOST));
        $path = (string) parse_url($this->trailer_url, PHP_URL_PATH);

        if (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com', 'youtu.be'], true)) {
            $videoId = null;

            if ($host === 'youtu.be') {
                $videoId = trim($path, '/');
            } elseif (str_starts_with($path, '/watch')) {
                parse_str((string) parse_url($this->trailer_url, PHP_URL_QUERY), $query);
                $videoId = $query['v'] ?? null;
            } elseif (preg_match('~^/(?:embed|shorts|live)/([^/]+)~', $path, $matches) === 1) {
                $videoId = $matches[1];
            }

            return is_string($videoId) && preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId) === 1
                ? "https://www.youtube-nocookie.com/embed/{$videoId}"
                : null;
        }

        if (in_array($host, ['vimeo.com', 'www.vimeo.com', 'player.vimeo.com'], true)
            && preg_match('~/(?:video/)?([0-9]+)$~', $path, $matches) === 1) {
            return "https://player.vimeo.com/video/{$matches[1]}";
        }

        return null;
    }

    public function getTrailerFileUrlAttribute(): ?string
    {
        if (! $this->trailer_url || parse_url($this->trailer_url, PHP_URL_SCHEME) !== 'https') {
            return null;
        }

        $path = (string) parse_url($this->trailer_url, PHP_URL_PATH);

        return preg_match('/\.(?:mp4|webm|ogv)$/i', $path) === 1
            ? $this->trailer_url
            : null;
    }
}
