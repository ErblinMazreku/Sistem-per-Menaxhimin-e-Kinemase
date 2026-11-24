@extends('layouts.app')

@section('content')
<div class="shell py-16">
    <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.25em] text-violet-300">The programme</p>
            <h1 class="mt-2 text-4xl font-bold">Find your next favourite</h1>
        </div>
        <form class="flex gap-2" method="GET" action="{{ route('movies.index') }}">
            <label class="sr-only" for="catalogue-query">Search cinema catalogue</label>
            <input id="catalogue-query" class="field min-w-0 md:w-64" name="q" value="{{ request('q') }}" placeholder="Search catalogue...">
            <button class="btn-primary !px-4">Search</button>
        </form>
    </div>

    <form action="{{ route('movies.search') }}" method="GET" class="glass mt-8 flex flex-col gap-4 rounded-2xl p-5 sm:flex-row sm:items-end">
        <div class="flex-1">
            <label for="tmdb-query" class="text-xs font-bold uppercase tracking-[.2em] text-violet-300">Discover more with TMDb</label>
            <input id="tmdb-query" class="field mt-3 w-full" name="query" required maxlength="100" placeholder="Search movies from the TMDb library...">
        </div>
        <button class="btn-primary shrink-0">Search TMDb</button>
    </form>

    <div class="mt-8 flex flex-wrap gap-2">
        <a href="{{ route('movies.index') }}" class="rounded-full px-4 py-2 text-sm {{ !request('genre') ? 'bg-violet-500 text-white' : 'border border-white/10 text-slate-400' }}">All films</a>
        @foreach($genres as $genre)
            <a href="{{ route('movies.index', ['genre' => $genre->id, 'q' => request('q')]) }}" class="rounded-full border border-white/10 px-4 py-2 text-sm text-slate-400 hover:border-violet-400 hover:text-white">{{ $genre->name }}</a>
        @endforeach
    </div>

    <section class="mt-12">
        <p class="text-xs font-bold uppercase tracking-[.25em] text-violet-300">Explore and watch</p>
        <h2 class="mt-2 text-3xl font-bold">Popular on TMDb</h2>
        <p class="mt-2 text-sm text-slate-400">Open a movie to play its trailer or find official streaming providers.</p>

        @if($tmdbError)
            <p role="status" class="mt-5 rounded-xl border border-amber-400/20 bg-amber-400/10 px-4 py-3 text-sm text-amber-100">{{ $tmdbError }}</p>
        @else
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @forelse($tmdbMovies as $tmdbMovie)
                    @continue(empty($tmdbMovie['id']))
                    <a href="{{ route('movies.show-tmdb', ['movieId' => $tmdbMovie['id']]) }}" class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[.04] transition hover:-translate-y-1 hover:border-violet-400/50">
                        @if(!empty($tmdbMovie['poster_path']))
                            <img class="aspect-[2/3] w-full object-cover" src="https://image.tmdb.org/t/p/w500{{ $tmdbMovie['poster_path'] }}" alt="{{ $tmdbMovie['title'] ?? 'Movie poster' }}" loading="lazy">
                        @else
                            <div class="grid aspect-[2/3] place-items-center bg-white/5 text-slate-500">Poster unavailable</div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-semibold">{{ $tmdbMovie['title'] ?? 'Untitled movie' }}</h3>
                            <p class="mt-1 text-sm text-slate-400">
                                {{ !empty($tmdbMovie['release_date']) ? substr($tmdbMovie['release_date'], 0, 4) : 'Release date unavailable' }}
                                @if(isset($tmdbMovie['vote_average']))
                                    · ★ {{ number_format((float) $tmdbMovie['vote_average'], 1) }}
                                @endif
                            </p>
                            <span class="mt-4 inline-block text-sm font-semibold text-violet-300">▶ Play trailer / Watch →</span>
                        </div>
                    </a>
                @empty
                    <p class="col-span-full text-slate-400">TMDb has no popular movies to display right now.</p>
                @endforelse
            </div>
        @endif
    </section>

    <section class="mt-16">
        <p class="text-xs font-bold uppercase tracking-[.25em] text-violet-300">Cinema catalogue</p>
        <h2 class="mt-2 text-3xl font-bold">Movies playing here</h2>
    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($movies as $movie)
            <a href="{{ route('movies.show', $movie) }}" class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[.04] transition hover:-translate-y-1 hover:border-violet-400/50">
                <img class="aspect-[2/3] w-full object-cover" src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" loading="lazy">
                <div class="p-4">
                    <h2 class="font-semibold">{{ $movie->title }}</h2>
                    <p class="mt-1 text-sm text-slate-400">{{ $movie->genre->name }} · {{ $movie->duration_minutes }} min</p>
                </div>
            </a>
        @empty
            <div class="col-span-full glass rounded-2xl p-10 text-center text-slate-400">No films matched your search.</div>
        @endforelse
    </div>

    <div class="mt-10">{{ $movies->links() }}</div>
    </section>
</div>
@endsection
