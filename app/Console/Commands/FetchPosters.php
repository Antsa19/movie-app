<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchPosters extends Command
{
    protected $signature = 'movies:fetch-posters';

    protected $description = 'Fetch movie poster links from TMDB';

    public function handle()
    {
        $token = env('TMDB_TOKEN');

        if (! $token) {
            $this->error('TMDB_TOKEN is missing in .env');

            return self::FAILURE;
        }

        foreach (Movie::whereNull('poster_url')->get() as $movie) {
            try {
                $response = Http::withoutVerifying()
                    ->withToken($token)
                    ->timeout(30)
                    ->retry(3, 1000)
                    ->get('https://api.themoviedb.org/3/search/movie', [
                        'query' => $movie->title,
                        'year' => $movie->release_year,
                    ]);

                $path = $response->json('results.0.poster_path');

                if ($path) {
                    $movie->update(['poster_url' => 'https://image.tmdb.org/t/p/w500' . $path]);
                    $this->info('OK: ' . $movie->title);
                } else {
                    $this->warn('Not found: ' . $movie->title);
                }
            } catch (\Throwable $e) {
                $this->error('Failed: ' . $movie->title);
            }
        }

        return self::SUCCESS;
    }
}
