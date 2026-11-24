<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MovieControllerTest extends TestCase
{
    public function test_tmdb_search_renders_results_and_uses_the_bearer_token(): void
    {
        config([
            'services.tmdb.base_url' => 'https://api.themoviedb.org/3',
            'services.tmdb.token' => 'test-access-token',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'api.themoviedb.org/3/search/movie*' => Http::response([
                'page' => 1,
                'total_pages' => 1,
                'results' => [
                    ['id' => 603, 'title' => 'The Matrix', 'overview' => 'A science-fiction film.'],
                ],
            ]),
        ]);

        $response = $this->get(route('movies.search', ['query' => 'The Matrix']));

        $response->assertOk()
            ->assertViewHas('movies', fn (array $movies): bool => $movies[0]['title'] === 'The Matrix')
            ->assertSee('Details and trailer');

        Http::assertSent(fn (ClientRequest $request): bool => $request->hasHeader('Authorization', 'Bearer test-access-token')
            && str_contains($request->url(), '/search/movie?')
        );
    }

    public function test_tmdb_movie_details_render_the_official_trailer_and_metadata(): void
    {
        config([
            'services.tmdb.base_url' => 'https://api.themoviedb.org/3',
            'services.tmdb.token' => 'test-access-token',
            'services.tmdb.region' => 'US',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'api.themoviedb.org/3/movie/603*' => Http::response([
                'id' => 603,
                'title' => 'The Matrix',
                'overview' => 'A science-fiction film.',
                'release_date' => '1999-03-30',
                'runtime' => 136,
                'genres' => [['id' => 28, 'name' => 'Action']],
                'watch/providers' => [
                    'results' => [
                        'US' => [
                            'link' => 'https://www.themoviedb.org/movie/603/watch',
                            'flatrate' => [
                                ['provider_name' => 'Netflix'],
                            ],
                        ],
                    ],
                ],
                'videos' => [
                    'results' => [
                        ['site' => 'YouTube', 'type' => 'Teaser', 'key' => 'teaser-key', 'name' => 'Teaser'],
                        ['site' => 'YouTube', 'type' => 'Trailer', 'key' => 'official-trailer-key', 'official' => true, 'name' => 'Official trailer'],
                    ],
                ],
            ]),
        ]);

        $response = $this->get(route('movies.show-tmdb', [
            'movieId' => 603,
            'query' => 'The Matrix',
        ]));

        $response->assertOk()
            ->assertSee('The Matrix')
            ->assertSee('1999-03-30')
            ->assertSee('youtube-nocookie.com/embed/official-trailer-key')
            ->assertSee('Play trailer on YouTube')
            ->assertSee('Netflix')
            ->assertSee('Availability data provided by JustWatch via TMDb.')
            ->assertSee('https://www.themoviedb.org/movie/603/watch')
            ->assertDontSee('youtube-nocookie.com/embed/teaser-key');

        Http::assertSent(function (ClientRequest $request): bool {
            $query = [];
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return ($query['append_to_response'] ?? null) === 'videos,watch/providers';
        });
    }

    public function test_tmdb_search_requires_a_query(): void
    {
        Http::preventStrayRequests();

        $response = $this->from(route('movies.index'))->get(route('movies.search'));

        $response->assertRedirect(route('movies.index'))
            ->assertSessionHasErrors('query');
    }

    public function test_tmdb_movie_details_return_404_when_tmdb_cannot_find_the_movie(): void
    {
        config([
            'services.tmdb.base_url' => 'https://api.themoviedb.org/3',
            'services.tmdb.token' => 'test-access-token',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'api.themoviedb.org/3/movie/999999*' => Http::response([], 404),
        ]);

        $response = $this->get(route('movies.show-tmdb', ['movieId' => 999999]));

        $response->assertNotFound();
    }

    public function test_tmdb_v3_api_key_is_sent_as_a_query_parameter(): void
    {
        config([
            'services.tmdb.base_url' => 'https://api.themoviedb.org/3',
            'services.tmdb.token' => null,
            'services.tmdb.key' => 'test-v3-api-key',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'api.themoviedb.org/3/search/movie*' => Http::response([
                'page' => 1,
                'total_pages' => 0,
                'results' => [],
            ]),
        ]);

        $response = $this->get(route('movies.search', ['query' => 'No match']));

        $response->assertOk();
        Http::assertSent(function (ClientRequest $request): bool {
            $query = [];
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return ($query['api_key'] ?? null) === 'test-v3-api-key';
        });
    }

    public function test_tmdb_v3_api_key_configured_as_tmdb_token_is_sent_as_a_query_parameter(): void
    {
        config([
            'services.tmdb.base_url' => 'https://api.themoviedb.org/3',
            'services.tmdb.token' => '0123456789abcdef0123456789abcdef',
            'services.tmdb.key' => null,
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'api.themoviedb.org/3/search/movie*' => Http::response([
                'page' => 1,
                'total_pages' => 0,
                'results' => [],
            ]),
        ]);

        $response = $this->get(route('movies.search', ['query' => 'No match']));

        $response->assertOk();
        Http::assertSent(function (ClientRequest $request): bool {
            $query = [];
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return ($query['api_key'] ?? null) === '0123456789abcdef0123456789abcdef'
                && ! $request->hasHeader('Authorization');
        });
    }
}
