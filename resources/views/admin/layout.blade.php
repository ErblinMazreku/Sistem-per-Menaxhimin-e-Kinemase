<!doctype html>
<html lang="en" class="bg-[#08080d]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin studio' }} · CineVerse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <div class="min-h-screen bg-[#08080d]">
        <header class="admin-topbar">
            <div class="flex items-center gap-4"><button class="admin-menu-button">☰</button><a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3"><span class="admin-brand-mark">✦</span><span class="text-lg font-bold tracking-tight text-white">Cine<span class="text-violet-400">Verse</span> <span class="ml-2 text-[10px] font-bold uppercase tracking-[.22em] text-violet-300">Admin studio</span></span></a></div>
            <div class="flex items-center gap-4"><div class="hidden text-right sm:block"><p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p><p class="text-[10px] uppercase tracking-widest text-violet-300">Administrator</p></div><div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div><form method="POST" action="{{ route('logout') }}">@csrf<button class="text-sm text-slate-400 transition hover:text-white">Log out</button></form></div>
        </header>
        <div class="admin-shell">
            <aside class="admin-sidebar">
                <div class="mb-8 rounded-2xl border border-violet-400/20 bg-violet-500/10 p-4"><p class="text-[10px] font-bold uppercase tracking-[.2em] text-violet-300">Control center</p><p class="mt-2 text-sm leading-5 text-slate-300">Manage your cinema from one place.</p></div>
                <p class="admin-nav-label">Workspace</p>
                <nav class="grid gap-1">
                    @foreach([['admin.dashboard','Overview','⌂'],['admin.movies.index','Movies','▣'],['admin.genres.index','Genres','◈'],['admin.halls.index','Halls & seats','▦'],['admin.showtimes.index','Showtimes','◷'],['admin.reservations.index','Reservations','◎']] as [$route, $label, $icon])
                        <a class="admin-nav-link {{ request()->routeIs($route) ? 'admin-nav-active' : '' }}" href="{{ route($route) }}"><span class="admin-nav-icon">{{ $icon }}</span><span>{{ $label }}</span>@if($route === 'admin.reservations.index' && isset($pendingReservations) && $pendingReservations > 0)<span class="ml-auto rounded-full bg-violet-400 px-2 py-0.5 text-[10px] text-white">{{ $pendingReservations }}</span>@endif</a>
                    @endforeach
                </nav>
                <div class="mt-auto hidden rounded-2xl border border-white/10 bg-white/[.03] p-4 lg:block"><p class="text-xs font-semibold text-white">Need the public view?</p><a class="mt-2 block text-xs text-violet-300 hover:text-white" href="{{ route('home') }}">Open website →</a></div>
            </aside>
            <main class="admin-main">
                @if(session('success'))<div class="mb-6 rounded-xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div>@endif
                @if($errors->any())<div class="mb-6 rounded-xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-200">{{ $errors->first() }}</div>@endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
