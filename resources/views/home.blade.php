@extends('layouts.app')
@section('content')
<section class="hero-shell relative overflow-hidden">
    <div class="hero-glow hero-glow-one"></div><div class="hero-glow hero-glow-two"></div>
    <div class="shell relative grid min-h-[680px] items-center gap-12 py-16 lg:grid-cols-[1.05fr_.95fr] lg:py-24">
        <div class="max-w-2xl">
            <div class="mb-7 inline-flex items-center gap-2 rounded-full border border-violet-300/20 bg-violet-300/10 px-4 py-2 text-xs font-bold uppercase tracking-[.2em] text-violet-200"><span class="live-dot"></span> Cinema, reimagined</div>
            <h1 class="hero-title text-5xl font-black leading-[.98] tracking-[-.05em] text-white sm:text-7xl lg:text-8xl">Your night out,<br><span>beautifully booked.</span></h1>
            <p class="mt-7 max-w-xl text-lg leading-8 text-slate-300">Big screen stories, perfectly chosen seats and an experience that starts before the lights go down.</p>
            <div class="mt-9 flex flex-wrap gap-3"><a class="btn-primary btn-shine" href="{{ route('movies.index') }}">Browse movies <span class="ml-3 text-lg">↗</span></a><a class="btn-secondary" href="#showtimes">Find a showtime</a></div>
            <div class="mt-12 grid max-w-lg grid-cols-3 gap-3 border-t border-white/10 pt-6"><div><p class="text-2xl font-bold text-white">8+</p><p class="mt-1 text-xs text-slate-500">films to discover</p></div><div><p class="text-2xl font-bold text-white">3</p><p class="mt-1 text-xs text-slate-500">premium screens</p></div><div><p class="text-2xl font-bold text-white">4.9<span class="text-violet-300">★</span></p><p class="mt-1 text-xs text-slate-500">guest rating</p></div></div>
        </div>
        @if($featured)
        <div class="relative mx-auto w-full max-w-[430px] lg:mr-4"><div class="poster-aura"></div><div class="poster-frame relative overflow-hidden rounded-[2rem] border border-white/20 bg-slate-900 p-2 shadow-2xl shadow-violet-950/50"><img class="aspect-[4/5] w-full rounded-[1.5rem] object-cover" src="{{ $featured->poster_url }}" alt="{{ $featured->title }}"><div class="poster-gradient absolute inset-2 rounded-[1.5rem]"></div><div class="absolute bottom-7 left-7 right-7"><p class="text-xs font-bold uppercase tracking-[.22em] text-violet-200">Spotlight pick</p><h2 class="mt-2 text-3xl font-bold text-white">{{ $featured->title }}</h2><div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-200"><span class="rounded-full bg-black/40 px-3 py-1 backdrop-blur">{{ $featured->genre->name }}</span><span class="rounded-full bg-black/40 px-3 py-1 backdrop-blur">{{ $featured->duration_minutes }} min</span><span class="rounded-full bg-black/40 px-3 py-1 backdrop-blur">{{ $featured->age_rating }}</span></div></div></div></div>
        @endif
    </div>
</section>

<section id="showtimes" class="shell relative z-10 scroll-mt-24 pt-8 md:pt-12">
    <div class="glass booking-strip grid gap-5 rounded-2xl p-5 md:grid-cols-[1fr_auto] md:items-center md:p-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.22em] text-violet-300">Tonight’s pick</p>
            <h2 class="mt-2 text-xl font-bold text-white">{{ $featured?->title ?? 'Ready for your next cinema night?' }}</h2>
            <p class="mt-2 text-sm text-slate-400">Find a screening and book your seats.</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row">
            @if($featured)
                <a href="{{ route('movies.show', $featured) }}" class="btn-primary !px-5 !py-3">See showtimes <span class="ml-2">→</span></a>
            @endif
            <a href="{{ route('movies.index') }}" class="btn-secondary !px-5 !py-3">Choose a movie</a>
        </div>
    </div>
</section>

<section class="shell py-24"><div class="mb-9 flex items-end justify-between gap-6"><div><p class="section-kicker">Curated programme</p><h2 class="section-title">Now showing</h2><p class="mt-3 text-slate-500">The stories worth leaving the house for.</p></div><a class="hidden text-sm font-semibold text-violet-300 hover:text-white sm:block" href="{{ route('movies.index') }}">See all movies <span class="ml-2">→</span></a></div><div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">@forelse($movies as $movie)<a href="{{ route('movies.show', $movie) }}" class="movie-card group"><div class="movie-art"><img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"><div class="movie-overlay"></div><span class="movie-rating">{{ $movie->age_rating }}</span><span class="movie-arrow">↗</span></div><div class="px-1 pt-4"><div class="flex items-start justify-between gap-3"><h3 class="text-lg font-bold text-white">{{ $movie->title }}</h3><span class="mt-1 text-xs text-slate-500">{{ $movie->duration_minutes }}m</span></div><p class="mt-2 text-sm text-slate-500">{{ $movie->genre->name }} <span class="mx-1 text-violet-400">•</span> Premium screening</p></div></a>@empty<div class="glass rounded-2xl p-8 text-slate-400">Your programme is being prepared.</div>@endforelse</div></section>

<section class="section-dark border-y border-white/5"><div class="shell grid gap-12 py-24 lg:grid-cols-[.8fr_1.2fr]"><div><p class="section-kicker">Pick your mood</p><h2 class="section-title max-w-md">There’s always a seat for the story you need.</h2><p class="mt-5 max-w-md leading-7 text-slate-400">From edge-of-your-seat thrillers to films that stay with you long after the credits, find your kind of night.</p></div><div class="grid grid-cols-2 gap-3 sm:grid-cols-3">@foreach($genres as $genre)<a href="{{ route('movies.index', ['genre' => $genre->id]) }}" class="genre-tile"><span class="genre-icon">{{ ['✦','◈','◉','✺','✧'][$loop->index % 5] }}</span><span class="mt-5 block font-semibold text-white">{{ $genre->name }}</span><span class="mt-1 block text-xs text-slate-500">{{ $genre->movies_count }} {{ $genre->movies_count === 1 ? 'film' : 'films' }}</span></a>@endforeach</div></div></section>

<section class="shell py-24"><div class="glass feature-panel relative overflow-hidden rounded-[2rem] p-8 sm:p-12"><div class="feature-orb"></div><div class="relative max-w-xl"><p class="section-kicker">The CineVerse standard</p><h2 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl">Less scrolling.<br>More “that was amazing.”</h2><div class="mt-8 grid gap-5 sm:grid-cols-3"><div><span class="feature-number">01</span><p class="mt-3 font-semibold text-white">Easy booking</p><p class="mt-2 text-sm leading-6 text-slate-500">See available seats before you commit.</p></div><div><span class="feature-number">02</span><p class="mt-3 font-semibold text-white">Real-time seats</p><p class="mt-2 text-sm leading-6 text-slate-500">Your seat is held safely during checkout.</p></div><div><span class="feature-number">03</span><p class="mt-3 font-semibold text-white">Your history</p><p class="mt-2 text-sm leading-6 text-slate-500">Every booking, right where you need it.</p></div></div></div></div></section>
<section class="shell pb-10" data-carousel>
    <div class="carousel-shell relative overflow-hidden rounded-3xl border border-white/10 bg-black/30">
        <div class="carousel-track" data-carousel-track>
            @foreach($featuredMovies as $movie)
                <article class="carousel-slide" data-carousel-slide><img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"><div class="carousel-slide-overlay"></div><div class="relative z-10 flex min-h-[220px] items-end justify-between gap-6 p-6 sm:p-10"><div><p class="text-xs font-bold uppercase tracking-[.22em] text-violet-300">Now showing</p><h2 class="mt-2 text-3xl font-black text-white sm:text-4xl">{{ $movie->title }}</h2><p class="mt-2 text-sm text-slate-300">{{ $movie->genre->name }} · {{ $movie->duration_minutes }} min · {{ $movie->age_rating }}</p></div><a class="btn-primary shrink-0" href="{{ route('movies.show', $movie) }}">Book tickets →</a></div></article>
            @endforeach
        </div>
        <button class="carousel-control carousel-prev" type="button" data-carousel-prev aria-label="Previous featured movie">‹</button><button class="carousel-control carousel-next" type="button" data-carousel-next aria-label="Next featured movie">›</button><div class="carousel-dots" data-carousel-dots></div>
    </div>
</section>
<section class="shell pt-16"><div class="search-panel relative overflow-hidden rounded-[1.75rem] border border-violet-300/15 p-6 sm:p-8"><div class="search-panel-glow"></div><div class="relative grid gap-7 lg:grid-cols-[.9fr_1.1fr] lg:items-center"><div><p class="section-kicker">Find your mood</p><h2 class="mt-3 text-2xl font-black text-white sm:text-3xl">What do you feel like watching?</h2><p class="mt-3 max-w-md text-sm leading-6 text-slate-400">Search the programme by title or jump straight into a genre and find your next screening in seconds.</p></div><form action="{{ route('movies.index') }}" method="GET" class="grid gap-3 sm:grid-cols-[1fr_auto]"><label class="sr-only" for="homepage-search">Search movies</label><input id="homepage-search" class="field !border-white/15 !bg-black/30" name="q" placeholder="Search by movie title..." autocomplete="off"><button class="btn-primary" type="submit">Search films <span class="ml-2">→</span></button><div class="flex flex-wrap gap-2 sm:col-span-2">@foreach($genres->take(5) as $genre)<a href="{{ route('movies.index', ['genre' => $genre->id]) }}" class="genre-pill">{{ $genre->name }} <span>↗</span></a>@endforeach</div></form></div></div></section>

@endsection
