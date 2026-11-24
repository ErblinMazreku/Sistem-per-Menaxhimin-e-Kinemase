<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TmdbController extends Controller
{
    public function popular(Request $request, TmdbService $tmdb): JsonResponse
    {
        $validated = $request->validate([
            'page' => ['sometimes', 'integer', 'min:1', 'max:500'],
        ]);

        return response()->json($tmdb->popularMovies((int) ($validated['page'] ?? 1)));
    }

    public function search(Request $request, TmdbService $tmdb): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1', 'max:500'],
        ]);

        return response()->json($tmdb->searchMovies(
            $validated['query'],
            (int) ($validated['page'] ?? 1),
        ));
    }

    public function show(int $movieId, TmdbService $tmdb): JsonResponse
    {
        try {
            return response()->json($tmdb->movie($movieId));
        } catch (RequestException $exception) {
            if ($exception->response->notFound()) {
                abort(404, 'Filmi nuk u gjet në TMDb.');
            }

            throw $exception;
        }
    }
}
