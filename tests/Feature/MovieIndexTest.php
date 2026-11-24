<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MovieIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_movies_page_displays_popular_tmdb_movies_for_discovery(): void
    {
        config([
            'services.tmdb.base_url' => 'https://api.themoviedb.org/3',
            'services.tmdb.token' => 'test-access-token',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'api.themoviedb.org/3/movie/popular*' => Http::response([
                'results' => [
                    [
                        'id' => 603,
                        'title' => 'The Matrix',
                        'poster_path' => '/matrix.jpg',
                        'release_date' => '1999-03-30',
                        'vote_average' => 8.7,
                    ],
                ],
            ]),
        ]);

        $response = $this->get(route('movies.index'));

        $response->assertOk()
            ->assertSee('Popular on TMDb')
            ->assertSee('The Matrix')
            ->assertSee(route('movies.show-tmdb', ['movieId' => 603]))
            ->assertSee('Play trailer / Watch');
    }

    public function test_movies_page_explains_when_tmdb_is_unavailable(): void
    {
        config([
            'services.tmdb.base_url' => 'https://api.themoviedb.org/3',
            'services.tmdb.token' => 'test-access-token',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'api.themoviedb.org/3/movie/popular*' => Http::response([], 503),
        ]);

        $response = $this->get(route('movies.index'));

        $response->assertOk()
            ->assertSee('Filmat nga TMDb nuk mund të ngarkohen tani.');
    }
}
