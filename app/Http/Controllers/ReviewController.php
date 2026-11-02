<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Movie $movie)
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
        ]);

        Review::updateOrCreate(
            ['user_id' => auth()->id(), 'movie_id' => $movie->id],
            $data
        );

        return redirect()->route('movies.show', $movie)->with('success', 'Your review is saved.');
    }

    public function destroy(Review $review)
    {
        abort_unless($review->user_id === auth()->id(), 403);

        $movie = $review->movie;
        $review->delete();

        return redirect()->route('movies.show', $movie)->with('success', 'Your review is deleted.');
    }
}
