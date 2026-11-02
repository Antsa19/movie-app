@extends('layouts.app')

@section('title', 'Manage Movies')

@section('content')
@php
    $genreColors = [
        'Action' => '#f97316',
        'Drama' => '#8b5cf6',
        'Sci-Fi' => '#3b82f6',
        'Comedy' => '#facc15',
        'Horror' => '#dc2626',
        'Animation' => '#10b981',
        'Romance' => '#ec4899',
        'Thriller' => '#14b8a6',
    ];
    $fallbackColors = ['#06b6d4', '#84cc16', '#a855f7', '#e11d48', '#0ea5e9', '#f59e0b'];
    $colorFor = fn ($name) => $genreColors[$name] ?? $fallbackColors[crc32($name) % count($fallbackColors)];
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <i class="bi bi-film fs-2 text-danger me-2"></i>
        <h3 class="mb-0">Manage Movies</h3>
    </div>
    <a href="{{ route('admin.movies.create') }}" class="btn btn-danger">
        <i class="bi bi-plus-lg"></i> Add movie
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Title</th>
                    <th>Genre</th>
                    <th>Director</th>
                    <th>Year</th>
                    <th>Duration</th>
                    <th>Rating</th>
                    <th class="text-end" style="width: 200px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movies as $movie)
                    <tr>
                        <td>{{ $movies->firstItem() + $loop->index }}</td>
                        <td class="fw-bold">{{ $movie->title }}</td>
                        <td>
                            <span class="badge" style="background-color: {{ $colorFor($movie->genre->name) }}; color: #fff;">
                                {{ $movie->genre->name }}
                            </span>
                        </td>
                        <td>{{ $movie->director }}</td>
                        <td>{{ $movie->release_year }}</td>
                        <td>{{ $movie->duration_minutes }} min</td>
                        <td>
                            @if($movie->reviews_avg_rating)
                                <i class="bi bi-star-fill text-warning"></i>
                                {{ number_format($movie->reviews_avg_rating, 1) }}
                                <span class="text-muted small">({{ $movie->reviews_count }})</span>
                            @else
                                <span class="text-muted">No reviews</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.movies.edit', $movie) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.movies.destroy', $movie) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this movie? All its reviews will also be deleted.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No movies yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $movies->links('pagination::bootstrap-5') }}
</div>
@endsection
