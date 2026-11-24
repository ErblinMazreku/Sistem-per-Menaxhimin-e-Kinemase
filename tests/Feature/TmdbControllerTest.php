<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TmdbControllerTest extends TestCase
{
    public function test_popular_movies_endpoint_returns_tmdb_movies(): void
    {
        config([
            'services.tmdb.base_url' => 'https://api.themoviedb.org/3',
            'services.tmdb.token' => 'test-access-token',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'api.themoviedb.org/3/movie/popular*' => Http::response([
                'page' => 2,
                'results' => [['id' => 603, 'title' => 'The Matrix']],
            ]),
        ]);

        $response = $this->getJson(route('api.movies.popular', ['page' => 2]));

        $response->assertOk()
            ->assertJsonPath('page', 2)
            ->assertJsonPath('results.0.title', 'The Matrix');

        Http::assertSent(fn (ClientRequest $request): bool => $request->hasHeader('Authorization', 'Bearer test-access-token')
            && str_contains($request->url(), '/movie/popular?')
        );
    }

    public function test_movie_search_endpoint_returns_tmdb_results(): void
    {
        config([
            'services.tmdb.base_url' => 'https://api.themoviedb.org/3',
            'services.tmdb.token' => 'test-access-token',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'api.themoviedb.org/3/search/movie*' => Http::response([
                'page' => 1,
                'results' => [['id' => 603, 'title' => 'The Matrix']],
            ]),
        ]);

        $response = $this->getJson(route('api.movies.search', ['query' => 'The Matrix']));

        $response->assertOk()
            ->assertJsonPath('results.0.id', 603)
            ->assertJsonPath('results.0.title', 'The Matrix');
    }

    public function test_movie_details_endpoint_includes_videos(): void
    {
        config([
            'services.tmdb.base_url' => 'https://api.themoviedb.org/3',
            'services.tmdb.token' => 'test-access-token',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'api.themoviedb.org/3/movie/603*' => Http::response([
                'id' => 603,
                'title' => 'The Matrix',
                'videos' => [
                    'results' => [
                        ['site' => 'YouTube', 'type' => 'Trailer', 'key' => 'trailer-key'],
                    ],
                ],
            ]),
        ]);

        $response = $this->getJson(route('api.movies.show', ['movieId' => 603]));

        $response->assertOk()
            ->assertJsonPath('title', 'The Matrix')
            ->assertJsonPath('videos.results.0.key', 'trailer-key');
    }

    public function test_movie_search_endpoint_returns_422_when_query_is_missing(): void
    {
        Http::preventStrayRequests();

        $response = $this->getJson(route('api.movies.search'));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('query');
    }

    public function test_movie_details_endpoint_returns_404_when_tmdb_cannot_find_the_movie(): void
    {
        config([
            'services.tmdb.base_url' => 'https://api.themoviedb.org/3',
            'services.tmdb.token' => 'test-access-token',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'api.themoviedb.org/3/movie/999999*' => Http::response([], 404),
        ]);

        $response = $this->getJson(route('api.movies.show', ['movieId' => 999999]));

        $response->assertNotFound();
    }
}
