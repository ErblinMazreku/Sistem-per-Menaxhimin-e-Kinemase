@extends('layouts.app')
@section('content')
<div class="shell py-16">
    <div class="grid gap-10 lg:grid-cols-[300px_1fr]">
        <img class="mx-auto aspect-[2/3] w-full max-w-xs rounded-3xl object-cover shadow-2xl shadow-violet-950/40" src="{{ $movie->poster_url }}" alt="{{ $movie->title }}">
        <div class="self-center">
            <p class="text-sm text-violet-300">{{ $movie->genre->name }} · {{ $movie->duration_minutes }} min · {{ $movie->age_rating }}</p>
            <h1 class="mt-3 text-5xl font-bold">{{ $movie->title }}</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-400">{{ $movie->description }}</p>
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
