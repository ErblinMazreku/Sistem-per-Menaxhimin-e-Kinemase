<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use App\Services\TmdbService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MovieController extends Controller
{
    public function index(Request $request, TmdbService $tmdb): View
    {
        $movies = Movie::with('genre')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->q.'%'))
            ->when($request->filled('genre'), fn ($q) => $q->where('genre_id', $request->genre))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $tmdbMovies = [];
        $tmdbError = null;

        if ($tmdb->isConfigured()) {
            try {
                $tmdbMovies = $tmdb->popularMovies()['results'] ?? [];
            } catch (ConnectionException|RequestException $exception) {
                report($exception);
                $tmdbError = 'Filmat nga TMDb nuk mund të ngarkohen tani. Kontrollo TMDB_TOKEN/API key dhe lidhjen me internetin.';
            }
        } else {
            $tmdbError = 'Shto TMDB_TOKEN ose TMDB_API_KEY në .env për të shfaqur filmat nga TMDb.';
        }

        return view('movies.index', [
            'movies' => $movies,
            'genres' => Genre::orderBy('name')->get(),
            'tmdbMovies' => is_array($tmdbMovies) ? $tmdbMovies : [],
            'tmdbError' => $tmdbError,
        ]);
    }

    public function show(Movie $movie): View
    {
        $movie->load(['genre', 'showtimes' => fn ($q) => $q->with('hall')->where('show_date', '>=', today())->orderBy('show_date')->orderBy('start_time')]);

        return view('movies.show', compact('movie'));
    }

    /**
     * 1. Kërkimi i filmave direkt nga TMDb API
     */
    public function searchTmdb(Request $request, TmdbService $tmdb): View
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1', 'max:500'],
        ]);
        $query = $validated['query'];
        $response = $tmdb->searchMovies($query, (int) ($validated['page'] ?? 1));

        return view('movies.tmdb-search', [
            'movies' => $response['results'] ?? [],
            'query' => $query,
            'page' => $response['page'] ?? (int) ($validated['page'] ?? 1),
            'totalPages' => $response['total_pages'] ?? 1,
        ]);
    }

    /**
     * 2. Shfaqja e detajeve dhe trailer-it të një filmi nga TMDb API sipas TMDb ID
     */
    public function showTmdb(Request $request, int $movieId, TmdbService $tmdb): View
    {
        try {
            $movie = $tmdb->movie($movieId);
        } catch (RequestException $exception) {
            if ($exception->response->notFound()) {
                abort(404, 'Filmi nuk u gjet në TMDb.');
            }

            throw $exception;
        }

        if ($movie === []) {
            abort(404, 'Filmi nuk u gjet në TMDb.');
        }

        $videos = is_array($movie['videos']['results'] ?? null)
            ? array_values(array_filter($movie['videos']['results'], 'is_array'))
            : [];

        $youtubeVideos = array_values(array_filter($videos, fn (array $video): bool => ($video['site'] ?? null) === 'YouTube'
            && in_array($video['type'] ?? null, ['Trailer', 'Teaser'], true)
            && ! empty($video['key'])
        ));
        $trailer = collect($youtubeVideos)->first(fn (array $video): bool => ($video['type'] ?? null) === 'Trailer' && ($video['official'] ?? false)
        ) ?? collect($youtubeVideos)->first(fn (array $video): bool => ($video['type'] ?? null) === 'Trailer'
        ) ?? collect($youtubeVideos)->first();
        $trailerKey = $trailer['key'] ?? null;

        if (! is_array($movie['genres'] ?? null)) {
            $movie['genres'] = [];
        }

        $region = config('services.tmdb.region', 'US');
        $watchProviders = $movie['watch/providers']['results'][$region] ?? [];
        $watchProviders = is_array($watchProviders) ? $watchProviders : [];

        return view('movies.tmdb-show', [
            'movie' => $movie,
            'trailerKey' => $trailerKey,
            'videos' => $videos,
            'watchProviders' => $watchProviders,
            'region' => $region,
            'query' => $request->query('query'),
        ]);
    }
}
