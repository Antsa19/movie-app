<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchActors extends Command
{
    protected $signature = 'movies:fetch-actors';

    protected $description = 'Fetch the main actors of each movie from TMDB';

    public function handle()
    {
        $token = env('TMDB_TOKEN');

        if (! $token) {
            $this->error('TMDB_TOKEN is missing in .env');

            return self::FAILURE;
        }

        foreach (Movie::whereNull('actors')->get() as $movie) {
            try {
                $search = Http::withoutVerifying()
                    ->withToken($token)
                    ->timeout(30)
                    ->retry(3, 1000)
                    ->get('https://api.themoviedb.org/3/search/movie', [
                        'query' => $movie->title,
                        'year' => $movie->release_year,
                    ]);

                $tmdbId = $search->json('results.0.id');

                if (! $tmdbId) {
                    $this->warn('Not found: ' . $movie->title);

                    continue;
                }

                $credits = Http::withoutVerifying()
                    ->withToken($token)
                    ->timeout(30)
                    ->retry(3, 1000)
                    ->get("https://api.themoviedb.org/3/movie/{$tmdbId}/credits");

                $names = collect($credits->json('cast'))
                    ->take(5)
                    ->pluck('name')
                    ->implode(', ');

                if ($names) {
                    $movie->update(['actors' => $names]);
                    $this->info('OK: ' . $movie->title . ' (' . $names . ')');
                } else {
                    $this->warn('No cast: ' . $movie->title);
                }
            } catch (\Throwable $e) {
                $this->error('Failed: ' . $movie->title);
            }
        }

        return self::SUCCESS;
    }
}
