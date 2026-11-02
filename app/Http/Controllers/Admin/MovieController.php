<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::with('genre')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(10);

        return view('admin.movies.index', compact('movies'));
    }

    public function create()
    {
        $genres = Genre::orderBy('name')->get();

        return view('admin.movies.create', compact('genres'));
    }

    public function store(Request $request)
    {
        Movie::create($this->validated($request));

        return redirect()->route('admin.movies.index')->with('success', 'Movie added.');
    }

    public function edit(Movie $movie)
    {
        $genres = Genre::orderBy('name')->get();

        return view('admin.movies.edit', compact('movie', 'genres'));
    }

    public function update(Request $request, Movie $movie)
    {
        $movie->update($this->validated($request));

        return redirect()->route('admin.movies.index')->with('success', 'Movie updated.');
    }

    public function destroy(Movie $movie)
    {
        $movie->delete();

        return redirect()->route('admin.movies.index')->with('success', 'Movie deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'genre_id' => ['required', 'exists:genres,id'],
            'title' => ['required', 'string', 'max:255'],
            'synopsis' => ['nullable', 'string'],
            'director' => ['required', 'string', 'max:255'],
            'release_year' => ['required', 'integer', 'min:1888', 'max:' . (date('Y') + 5)],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1000'],
            'poster_url' => ['nullable', 'url', 'max:255'],
        ]);
    }
}
