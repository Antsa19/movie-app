<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $movies = Movie::with('genre')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
        $q->where('title', 'like', '%' . $search . '%')
          ->orWhere('actors', 'like', '%' . $search . '%');
    });

            })
            ->when($request->filled('genre'), function ($query) use ($request) {
                $query->where('genre_id', $request->genre);
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $genres = Genre::orderBy('name')->get();

        return view('movies.index', compact('movies', 'genres'));
    }

    public function show(Movie $movie)
    {
        $movie->load(['genre', 'reviews.user', 'reviews.reply.admin']);
        $movie->loadAvg('reviews', 'rating');

        $myReview = $movie->reviews->firstWhere('user_id', auth()->id());

        return view('movies.show', compact('movie', 'myReview'));
    }
}