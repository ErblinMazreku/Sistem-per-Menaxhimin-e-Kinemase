<!doctype html>
<html lang="en" class="bg-[#07070b]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CineVerse' }} · CineVerse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="sticky top-0 z-30 border-b border-white/10 bg-[#07070b]/85 backdrop-blur-xl">
        <div class="shell flex h-20 items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-violet-500 text-lg shadow-lg shadow-violet-500/25">✦</span>
                <span class="text-lg font-bold tracking-tight">Cine<span class="text-violet-400">Verse</span></span>
            </a>

            <button type="button" class="mobile-menu-toggle" data-menu-toggle aria-controls="site-menu" aria-expanded="false">☰</button>
            <div id="site-menu" class="site-menu hidden items-center gap-8 text-sm text-slate-400 md:flex">
                <a class="transition hover:text-white" href="{{ route('movies.index') }}">Explore movies</a>
                <a class="transition hover:text-white" href="{{ route('home') }}#showtimes">Showtimes</a>
                @auth
                    <a class="transition hover:text-white" href="{{ route('reservations.index') }}">My bookings</a>
                    @if(auth()->user()->isAdmin())
                        <a class="text-violet-300" href="{{ route('admin.dashboard') }}">Admin studio</a>
                    @endif
                @endauth
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn-secondary !px-4 !py-2">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-slate-300 hover:text-white">Sign in</a>
                    <a href="{{ route('register') }}" class="btn-primary !px-4 !py-2">Join free</a>
                @endauth
            </div>
        </div>
        <div class="mobile-menu-panel hidden" data-menu-panel>
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('movies.index') }}">Explore movies</a>
            <a href="{{ route('home') }}#showtimes">Showtimes</a>
            @auth<a href="{{ route('reservations.index') }}">My bookings</a>@else<a href="{{ route('login') }}">Sign in</a>@endauth
        </div>
    </nav>

    @if(session('success'))
        <div class="shell pt-5"><div class="rounded-xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div></div>
    @endif
    @if($errors->any())
        <div class="shell pt-5"><div class="rounded-xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-200">{{ $errors->first() }}</div></div>
    @endif

    <main>@yield('content')</main>

    <footer class="mt-24 border-t border-white/10 py-10">
        <div class="shell flex flex-col justify-between gap-5 text-sm text-slate-500 sm:flex-row">
            <p>© {{ date('Y') }} CineVerse. Stories worth showing up for.</p>
            <p>Premium cinema · Thoughtfully curated.</p>
        </div>
    </footer>
</body>
</html>
