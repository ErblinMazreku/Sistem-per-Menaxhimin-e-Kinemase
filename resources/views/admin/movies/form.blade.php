@extends('admin.layout')

@section('content')
<h1 class="text-3xl font-bold">{{ $movie->exists ? 'Edit movie' : 'Add movie' }}</h1>

<form class="glass mt-8 max-w-2xl space-y-5 rounded-2xl p-6" method="POST" action="{{ $movie->exists ? route('admin.movies.update', $movie) : route('admin.movies.store') }}">
    @csrf
    @if($movie->exists)
        @method('PUT')
    @endif

    <div>
        <label class="label" for="title">Title</label>
        <input id="title" class="field" name="title" value="{{ old('title', $movie->title) }}" required>
        @error('title')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="label" for="genre_id">Genre</label>
        <select id="genre_id" class="field" name="genre_id" required>
            @foreach($genres as $genre)
                <option value="{{ $genre->id }}" @selected(old('genre_id', $movie->genre_id) == $genre->id)>{{ $genre->name }}</option>
            @endforeach
        </select>
        @error('genre_id')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="label" for="description">Description</label>
        <textarea id="description" class="field" name="description" rows="5" required>{{ old('description', $movie->description) }}</textarea>
        @error('description')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-3">
        <div>
            <label class="label" for="duration_minutes">Minutes</label>
            <input id="duration_minutes" class="field" type="number" name="duration_minutes" value="{{ old('duration_minutes', $movie->duration_minutes) }}" required>
            @error('duration_minutes')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="age_rating">Rating</label>
            <input id="age_rating" class="field" name="age_rating" value="{{ old('age_rating', $movie->age_rating ?: 'PG-13') }}" required>
            @error('age_rating')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="poster_path">Poster URL</label>
            <input id="poster_path" class="field" name="poster_path" type="url" value="{{ old('poster_path', $movie->poster_path) }}">
            @error('poster_path')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="label" for="trailer_url">Trailer URL</label>
        <input id="trailer_url" class="field" name="trailer_url" type="url" inputmode="url" placeholder="https://www.youtube.com/watch?v=..." value="{{ old('trailer_url', $movie->trailer_url) }}">
        <p class="mt-2 text-sm text-slate-400">Add an HTTPS YouTube, Vimeo, or direct MP4/WebM trailer link. YouTube/Vimeo videos play on the movie page.</p>
        @error('trailer_url')<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
    </div>

    <button class="btn-primary">Save movie</button>
</form>
@endsection
