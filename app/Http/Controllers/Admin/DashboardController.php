<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Reply;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totals = [
            'users' => User::count(),
            'movies' => Movie::count(),
            'genres' => Genre::count(),
            'reviews' => Review::count(),
            'replies' => Reply::count(),
            'average_rating' => round(Review::avg('rating') ?? 0, 2),
        ];

        // Join: genres + movies
        $moviesPerGenre = DB::table('genres')
            ->leftJoin('movies', 'movies.genre_id', '=', 'genres.id')
            ->select('genres.name', DB::raw('COUNT(movies.id) as total'))
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('total')
            ->get();

        // Join: movies + genres + reviews
        $topMovies = DB::table('movies')
            ->join('genres', 'genres.id', '=', 'movies.genre_id')
            ->join('reviews', 'reviews.movie_id', '=', 'movies.id')
            ->select(
                'movies.title',
                'genres.name as genre',
                DB::raw('AVG(reviews.rating) as avg_rating'),
                DB::raw('COUNT(reviews.id) as total_reviews')
            )
            ->groupBy('movies.id', 'movies.title', 'genres.name')
            ->orderByDesc('avg_rating')
            ->limit(5)
            ->get();

        // Join: users + reviews
        $topReviewers = DB::table('users')
            ->join('reviews', 'reviews.user_id', '=', 'users.id')
            ->select(
                'users.name',
                'users.email',
                DB::raw('COUNT(reviews.id) as total_reviews'),
                DB::raw('AVG(reviews.rating) as avg_rating')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_reviews')
            ->limit(5)
            ->get();

        $ratingDistribution = DB::table('reviews')
            ->select('rating', DB::raw('COUNT(*) as total'))
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();

        $latestReviews = Review::with(['user', 'movie'])->latest()->limit(5)->get();

        $moviesWithoutReviews = Movie::with('genre')->doesntHave('reviews')->get();

        // Join: reviews + replies (reply report)
        $repliedReviews = $totals['replies'];
        $pendingReviewsCount = Review::doesntHave('reply')->count();
        $replyRate = $totals['reviews'] > 0
            ? round(($repliedReviews / $totals['reviews']) * 100)
            : 0;

        $pendingReviews = Review::with(['user', 'movie'])
            ->doesntHave('reply')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totals',
            'moviesPerGenre',
            'topMovies',
            'topReviewers',
            'ratingDistribution',
            'latestReviews',
            'moviesWithoutReviews',
            'repliedReviews',
            'pendingReviewsCount',
            'replyRate',
            'pendingReviews'
        ));
    }
}
