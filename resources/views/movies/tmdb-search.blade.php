@extends('layouts.app')

@section('content')
<div class="shell py-16">
    <a href="{{ route('movies.index') }}" class="text-sm text-violet-300 hover:text-white">← Back to cinema catalogue</a>
    <div class="mt-6 flex flex-col justify-between gap-6 md:flex-row md:items-end">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.25em] text-violet-300">TMDb discovery</p>
            <h1 class="mt-2 text-4xl font-bold">Results for “{{ $query }}”</h1>
        </div>
        <form action="{{ route('movies.search') }}" method="GET" class="flex gap-2">
            <label class="sr-only" for="tmdb-query">Search TMDb</label>
            <input id="tmdb-query" class="field min-w-0 md:w-72" name="query" value="{{ $query }}" required maxlength="100" placeholder="Search all movies...">
            <button class="btn-primary !px-4">Search</button>
        </form>
    </div>

    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($movies as $item)
            <article class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[.04] transition hover:-translate-y-1 hover:border-violet-400/50">
                <a href="{{ route('movies.show-tmdb', ['movieId' => $item['id'], 'query' => $query]) }}">
                    @if(!empty($item['poster_path']))
                        <img class="aspect-[2/3] w-full object-cover" src="https://image.tmdb.org/t/p/w500{{ $item['poster_path'] }}" alt="{{ $item['title'] ?? 'Movie poster' }}" loading="lazy">
                    @else
                        <div class="grid aspect-[2/3] place-items-center bg-white/5 text-slate-500">Poster unavailable</div>
                    @endif
                    <div class="p-4">
                        <h2 class="font-semibold">{{ $item['title'] ?? 'Untitled movie' }}</h2>
                        <p class="mt-1 text-sm text-slate-400">
                            {{ !empty($item['release_date']) ? substr($item['release_date'], 0, 4) : 'Release date unavailable' }}
                            @if(isset($item['vote_average']))
                                · ★ {{ number_format((float) $item['vote_average'], 1) }}
                            @endif
                        </p>
                        <p class="mt-3 text-sm leading-6 text-slate-500">{{ \Illuminate\Support\Str::limit($item['overview'] ?? 'No synopsis available.', 150) }}</p>
                        <span class="mt-4 inline-block text-sm font-semibold text-violet-300">Details and trailer →</span>
                    </div>
                </a>
            </article>
        @empty
            <p class="col-span-full glass rounded-2xl p-10 text-center text-slate-400">No TMDb movies matched this search.</p>
        @endforelse
    </div>

    @if(($totalPages ?? 1) > 1)
        <nav class="mt-10 flex items-center justify-between" aria-label="TMDb search pages">
            @if($page > 1)
                <a class="btn-secondary" href="{{ route('movies.search', ['query' => $query, 'page' => $page - 1]) }}">← Previous</a>
            @else
                <span></span>
            @endif
            <span class="text-sm text-slate-400">Page {{ $page }} of {{ $totalPages }}</span>
            @if($page < $totalPages)
                <a class="btn-secondary" href="{{ route('movies.search', ['query' => $query, 'page' => $page + 1]) }}">Next →</a>
            @endif
        </nav>
    @endif
</div>
@endsection
