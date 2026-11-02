<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Genre;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@cinema.test');
        User::updateOrCreate(['email' => $adminEmail], ['name' => 'Cinema Admin', 'password' => Hash::make(env('ADMIN_PASSWORD', 'ChangeMe123!')), 'role' => 'admin']);
        User::updateOrCreate(['email' => 'guest@cinema.test'], ['name' => 'Demo Customer', 'password' => Hash::make('ChangeMe123!'), 'role' => 'customer']);

        $posters = [
            'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=800&q=85',
            'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?auto=format&fit=crop&w=800&q=85',
            'https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=800&q=85',
            'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=800&q=85',
        ];
        foreach (['Action', 'Drama', 'Sci-Fi', 'Comedy', 'Thriller'] as $name) Genre::firstOrCreate(['name' => $name]);
        $titles = [['Neon Horizon', 'Sci-Fi', 128, '16+'], ['The Last Frame', 'Drama', 114, '12+'], ['Midnight Runway', 'Thriller', 106, '16+'], ['Solaris Avenue', 'Comedy', 98, 'PG-13'], ['Echoes of Tomorrow', 'Action', 121, '12+'], ['Velvet Static', 'Drama', 109, '16+'], ['The Long Goodbye', 'Thriller', 116, '16+'], ['Parallel Hearts', 'Sci-Fi', 102, 'PG-13']];
        foreach ($titles as $i => [$title, $genre, $duration, $rating]) Movie::updateOrCreate(['title' => $title], ['genre_id' => Genre::where('name', $genre)->value('id'), 'description' => 'A beautifully crafted cinematic journey about the choices that shape who we become.', 'duration_minutes' => $duration, 'age_rating' => $rating, 'poster_path' => $posters[$i % count($posters)]]);
        foreach ([['Grand Hall', 60], ['Studio Two', 40], ['Luna Screen', 30]] as [$name, $capacity]) { $hall = Hall::firstOrCreate(['name' => $name], ['capacity' => $capacity]); if (! $hall->seats()->exists()) for ($i = 0; $i < $capacity; $i++) $hall->seats()->create(['row_label' => chr(65 + intdiv($i, 10)), 'seat_number' => ($i % 10) + 1]); }
        $hallIds = Hall::pluck('id')->values();
        foreach (Movie::limit(8)->get() as $i => $movie) Showtime::firstOrCreate(['movie_id' => $movie->id, 'hall_id' => $hallIds[$i % $hallIds->count()], 'show_date' => now()->addDays(($i % 5) + 1)->toDateString(), 'start_time' => ['14:00', '17:00', '20:15'][$i % 3]], ['ticket_price' => 12.50 + ($i % 3) * 2.5]);
    }
}
