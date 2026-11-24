<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class TmdbService
{
    public function searchMovies(string $query, int $page = 1): array
    {
        return $this->get('search/movie', [
            'query' => $query,
            'page' => $page,
        ]);
    }

    public function popularMovies(int $page = 1): array
    {
        return $this->get('movie/popular', ['page' => $page]);
    }

    public function movie(int $movieId): array
    {
        return $this->get("movie/{$movieId}", [
            'append_to_response' => 'videos,watch/providers',
        ]);
    }

    public function isConfigured(): bool
    {
        return filled(config('services.tmdb.token')) || filled(config('services.tmdb.key'));
    }

    private function get(string $endpoint, array $query = []): array
    {
        $token = config('services.tmdb.token');
        $apiKey = config('services.tmdb.key');

        $request = Http::acceptJson()
            ->connectTimeout(3)
            ->timeout(10);

        if (filled($apiKey)) {
            $query['api_key'] = $apiKey;
        } elseif (filled($token) && preg_match('/^[a-f0-9]{32}$/i', $token) === 1) {
            $query['api_key'] = $token;
        } elseif (filled($token)) {
            $request = $request->withToken($token);
        } else {
            throw new RuntimeException('Configure TMDB_TOKEN or TMDB_API_KEY to use TMDb.');
        }

        $baseUrl = rtrim((string) config('services.tmdb.base_url', 'https://api.themoviedb.org/3'), '/');
        $data = $request->get("{$baseUrl}/{$endpoint}", $query)->throw()->json();

        if (! is_array($data)) {
            throw new RuntimeException('TMDb returned an invalid response.');
        }

        return $data;
    }
}
