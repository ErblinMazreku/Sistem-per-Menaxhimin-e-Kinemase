@extends('layouts.app')

@section('content')
<div class="shell py-16">
    @if(!empty($query))
        <a href="{{ route('movies.search', ['query' => $query]) }}" class="text-sm text-violet-300 hover:text-white">← Back to TMDb search</a>
    @else
        <a href="{{ route('movies.index') }}" class="text-sm text-violet-300 hover:text-white">← Back to cinema catalogue</a>
    @endif

    <section class="mt-6 grid gap-10 lg:grid-cols-[300px_1fr]">
        @if(!empty($movie['poster_path']))
            <img class="mx-auto aspect-[2/3] w-full max-w-xs rounded-3xl object-cover shadow-2xl shadow-violet-950/40" src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}" alt="{{ $movie['title'] ?? 'Movie poster' }}">
        @else
            <div class="mx-auto grid aspect-[2/3] w-full max-w-xs place-items-center rounded-3xl bg-white/5 text-slate-500">Poster unavailable</div>
        @endif

        <div class="self-center">
            @if(!empty($movie['genres']))
                <p class="text-sm text-violet-300">{{ implode(' · ', array_column($movie['genres'], 'name')) }}</p>
            @endif
            <h1 class="mt-3 text-5xl font-bold">{{ $movie['title'] ?? 'Untitled movie' }}</h1>
            <p class="mt-6 max-w-3xl text-lg leading-8 text-slate-300">{{ $movie['overview'] ?? 'No synopsis available.' }}</p>

            @if($trailerKey)
                <a class="btn-primary mt-6 inline-flex items-center gap-2" href="https://www.youtube.com/watch?v={{ $trailerKey }}" target="_blank" rel="noopener noreferrer">
                    <span aria-hidden="true">▶</span> Play trailer on YouTube
                </a>
            @endif

            <dl class="mt-8 grid max-w-2xl grid-cols-2 gap-4 sm:grid-cols-3">
                @if(!empty($movie['release_date']))
                    <div class="glass rounded-xl p-4"><dt class="text-xs uppercase tracking-wider text-slate-500">Release date</dt><dd class="mt-2 font-semibold">{{ $movie['release_date'] }}</dd></div>
                @endif
                @if(!empty($movie['runtime']))
                    <div class="glass rounded-xl p-4"><dt class="text-xs uppercase tracking-wider text-slate-500">Runtime</dt><dd class="mt-2 font-semibold">{{ $movie['runtime'] }} min</dd></div>
                @endif
                @if(isset($movie['vote_average']))
                    <div class="glass rounded-xl p-4"><dt class="text-xs uppercase tracking-wider text-slate-500">TMDb rating</dt><dd class="mt-2 font-semibold">★ {{ number_format((float) $movie['vote_average'], 1) }}/10</dd></div>
                @endif
                @if(!empty($movie['status']))
                    <div class="glass rounded-xl p-4"><dt class="text-xs uppercase tracking-wider text-slate-500">Status</dt><dd class="mt-2 font-semibold">{{ $movie['status'] }}</dd></div>
                @endif
                @if(isset($movie['budget']) && $movie['budget'] > 0)
                    <div class="glass rounded-xl p-4"><dt class="text-xs uppercase tracking-wider text-slate-500">Budget</dt><dd class="mt-2 font-semibold">${{ number_format($movie['budget']) }}</dd></div>
                @endif
                @if(isset($movie['revenue']) && $movie['revenue'] > 0)
                    <div class="glass rounded-xl p-4"><dt class="text-xs uppercase tracking-wider text-slate-500">Revenue</dt><dd class="mt-2 font-semibold">${{ number_format($movie['revenue']) }}</dd></div>
                @endif
            </dl>

            @if(!empty($movie['homepage']) && \Illuminate\Support\Str::startsWith($movie['homepage'], ['https://', 'http://']))
                <a class="btn-secondary mt-6 inline-flex" href="{{ $movie['homepage'] }}" target="_blank" rel="noopener noreferrer">Official movie website ↗</a>
            @endif
        </div>
    </section>

    @php($watchLink = $watchProviders['link'] ?? null)
    @if($watchLink && \Illuminate\Support\Str::startsWith($watchLink, 'https://'))
        <section class="mt-12 glass rounded-2xl p-6">
            <p class="text-xs font-bold uppercase tracking-[.25em] text-violet-300">Official streaming options · {{ $region }}</p>
            <h2 class="mt-2 text-2xl font-bold">Where to watch</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach(['flatrate' => 'Stream', 'free' => 'Free', 'ads' => 'Free with ads', 'rent' => 'Rent', 'buy' => 'Buy'] as $providerType => $label)
                    @if(!empty($watchProviders[$providerType]))
                        <div>
                            <h3 class="text-sm font-semibold text-slate-300">{{ $label }}</h3>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($watchProviders[$providerType] as $provider)
                                    <a class="rounded-full border border-white/10 px-3 py-2 text-sm text-violet-200 transition hover:border-violet-400/50 hover:text-white" href="{{ $watchLink }}" target="_blank" rel="noopener noreferrer">
                                        {{ $provider['provider_name'] ?? 'Streaming provider' }} ↗
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <a class="mt-5 inline-block text-sm text-slate-400 underline underline-offset-4 hover:text-white" href="{{ $watchLink }}" target="_blank" rel="noopener noreferrer">See availability on TMDb</a>
            <p class="mt-3 text-xs text-slate-500">Availability data provided by JustWatch via TMDb.</p>
        </section>
    @endif

    <section class="mt-16">
        <p class="text-xs font-bold uppercase tracking-[.25em] text-violet-300">Watch</p>
        <h2 class="mt-2 text-3xl font-bold">Trailer and videos</h2>
        @if($trailerKey)
            <div class="relative mt-6 w-full overflow-hidden rounded-2xl border border-white/10 bg-black" style="aspect-ratio: 16 / 9;">
                <iframe
                    class="absolute inset-0 h-full w-full"
                    src="https://www.youtube-nocookie.com/embed/{{ $trailerKey }}"
                    title="{{ $movie['title'] ?? 'Movie' }} trailer"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen>
                </iframe>
            </div>
        @else
            <p class="mt-5 text-slate-400">No YouTube trailer or teaser is currently listed for this movie.</p>
        @endif

        @php($supportedVideos = array_filter($videos, fn (array $video): bool => in_array($video['site'] ?? null, ['YouTube', 'Vimeo'], true) && !empty($video['key'])))
        @if($supportedVideos)
            <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($supportedVideos as $video)
                    @php($videoUrl = ($video['site'] ?? '') === 'YouTube' ? 'https://www.youtube.com/watch?v='.$video['key'] : 'https://vimeo.com/'.$video['key'])
                    <a class="glass rounded-xl p-4 transition hover:border-violet-400/50" href="{{ $videoUrl }}" target="_blank" rel="noopener noreferrer">
                        <span class="font-semibold">{{ $video['name'] ?? 'Movie video' }}</span>
                        <span class="mt-1 block text-sm text-slate-400">{{ $video['type'] ?? 'Video' }} · {{ $video['site'] }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
