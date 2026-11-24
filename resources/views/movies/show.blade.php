@extends('layouts.app')
@section('content')
<div class="shell py-16">
    <div class="grid gap-10 lg:grid-cols-[300px_1fr]">
        <img class="mx-auto aspect-[2/3] w-full max-w-xs rounded-3xl object-cover shadow-2xl shadow-violet-950/40" src="{{ $movie->poster_url }}" alt="{{ $movie->title }}">
        <div class="self-center">
            <p class="text-sm text-violet-300">{{ $movie->genre->name }} · {{ $movie->duration_minutes }} min · {{ $movie->age_rating }}</p>
            <h1 class="mt-3 text-5xl font-bold">{{ $movie->title }}</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-400">{{ $movie->description }}</p>

            <section class="mt-10">
                <p class="text-xs font-bold uppercase tracking-[.2em] text-slate-400">Trailer</p>
                @if($movie->trailer_embed_url)
                    <div class="mt-4">
                        <a class="btn-primary inline-flex items-center gap-2" href="{{ $movie->trailer_url }}" target="_blank" rel="noopener noreferrer">
                            <span aria-hidden="true">▶</span> Play trailer
                        </a>
                        <div class="relative mt-5 w-full overflow-hidden rounded-2xl border border-white/10 bg-black" style="aspect-ratio: 16 / 9;">
                            <iframe
                                class="absolute inset-0 h-full w-full"
                                src="{{ $movie->trailer_embed_url }}"
                                title="{{ $movie->title }} trailer"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                @elseif($movie->trailer_file_url)
                    <video class="mt-4 aspect-video w-full rounded-2xl border border-white/10 bg-black" controls playsinline preload="metadata">
                        <source src="{{ $movie->trailer_file_url }}">
                        Your browser does not support HTML video.
                    </video>
                @else
                    <div class="mt-4 rounded-2xl border border-white/10 bg-white/[.04] p-5">
                        <p class="text-sm text-slate-300">Nuk është shtuar ende trailer për këtë film.</p>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <a class="btn-secondary !px-4 !py-2" href="https://www.youtube.com/results?search_query={{ urlencode($movie->title.' official trailer') }}" target="_blank" rel="noopener noreferrer">Kërko trailer në YouTube ↗</a>
                            @auth
                                @if(auth()->user()->isAdmin())
                                    <a class="btn-primary !px-4 !py-2" href="{{ route('admin.movies.edit', $movie) }}">Shto trailerin</a>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endif
            </section>

            <div class="mt-10">
                <p class="mb-4 text-xs font-bold uppercase tracking-[.2em] text-slate-400">Available showtimes</p>
                <div class="grid gap-3 sm:grid-cols-2">
                    @forelse($movie->showtimes as $showtime)
                        <a href="{{ route('bookings.create', $showtime) }}" class="glass rounded-2xl p-4 transition hover:border-violet-400/60">
                            <div class="flex justify-between"><span class="font-semibold">{{ $showtime->show_date->format('D, M j') }}</span><span class="text-violet-300">${{ number_format($showtime->ticket_price, 2) }}</span></div>
                            <p class="mt-2 text-sm text-slate-400">{{ \Carbon\Carbon::parse($showtime->start_time)->format('g:i A') }} · {{ $showtime->hall->name }}</p>
                        </a>
                    @empty
                        <p class="text-slate-500">No upcoming showtimes yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
