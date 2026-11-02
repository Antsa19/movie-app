@extends('layouts.app')

@section('title', 'Movies')

@section('content')
@php
    $genreColors = [
        'Action' => '#f97316',
        'Drama' => '#8b5cf6',
        'Sci-Fi' => '#3b82f6',
        'Comedy' => '#eab308',
        'Horror' => '#dc2626',
        'Animation' => '#10b981',
        'Romance' => '#ec4899',
        'Thriller' => '#14b8a6',
    ];
    $genreIcons = [
        'Action' => 'bi-lightning-charge-fill',
        'Drama' => 'bi-emoji-neutral-fill',
        'Sci-Fi' => 'bi-rocket-takeoff-fill',
        'Comedy' => 'bi-emoji-laughing-fill',
        'Horror' => 'bi-eye-fill',
        'Animation' => 'bi-palette-fill',
        'Romance' => 'bi-heart-fill',
        'Thriller' => 'bi-search',
    ];
    $fallbackColors = ['#06b6d4', '#84cc16', '#a855f7', '#e11d48', '#0ea5e9', '#f59e0b'];
    $colorFor = fn ($name) => $genreColors[$name] ?? $fallbackColors[crc32($name) % count($fallbackColors)];
    $iconFor = fn ($name) => $genreIcons[$name] ?? 'bi-film';

    $stars = function ($value) {
        $rounded = (int) round($value);
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $html .= '<i class="bi ' . ($i <= $rounded ? 'bi-star-fill' : 'bi-star') . '"></i> ';
        }
        return new \Illuminate\Support\HtmlString($html);
    };
@endphp

<style>
    .hero-banner {
        background: linear-gradient(135deg, #6366f1, #ec4899 55%, #f97316);
        color: #fff;
        border-radius: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .hero-banner .hero-icon {
        position: absolute;
        right: 24px;
        bottom: -18px;
        font-size: 9rem;
        opacity: .18;
        transform: rotate(-12deg);
    }
    .genre-chip {
        border-radius: 50rem;
        padding: .35rem .9rem;
        font-size: .875rem;
        text-decoration: none;
        border: 2px solid var(--chip-color);
        color: var(--chip-color);
        background: transparent;
        transition: all .15s ease;
        white-space: nowrap;
    }
    .genre-chip:hover,
    .genre-chip.active {
        background: var(--chip-color);
        color: #fff;
    }
    .movie-card {
        border: 0;
        border-radius: 1rem;
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .movie-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, .25) !important;
    }
    .poster-box {
        position: relative;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        overflow: hidden;
    }
    .poster-box img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .poster-box .poster-icon {
        font-size: 5rem;
        opacity: .85;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, .25));
    }
    .poster-box .poster-bg-icon {
        position: absolute;
        font-size: 11rem;
        opacity: .12;
        right: -20px;
        bottom: -30px;
        transform: rotate(-15deg);
    }
    .poster-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(0, 0, 0, .45);
        color: #fff;
        border-radius: 50rem;
        padding: .2rem .65rem;
        font-size: .75rem;
        backdrop-filter: blur(4px);
    }
    .poster-rating {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #fff;
        color: #111;
        border-radius: 50rem;
        padding: .2rem .6rem;
        font-size: .8rem;
        font-weight: 700;
    }
</style>

{{-- Hero --}}
<div class="hero-banner p-4 p-md-5 mb-4 shadow-sm">
    <i class="bi bi-camera-reels-fill hero-icon"></i>
    <div class="position-relative">
        <h2 class="fw-bold mb-1"><i class="bi bi-film me-2"></i>Discover movies</h2>
        <p class="mb-3 opacity-75">{{ $movies->total() }} movies waiting for your review</p>

        <form method="GET" action="{{ route('movies.index') }}" class="row g-2" style="max-width: 520px;">
            @if(request('genre'))
                <input type="hidden" name="genre" value="{{ request('genre') }}">
            @endif
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control border-0" placeholder="Search by title">
                </div>
            </div>
            <div class="col-auto">
                <button class="btn btn-dark"><i class="bi bi-funnel-fill"></i> Search</button>
            </div>
        </form>
    </div>
</div>

{{-- Genre chips --}}
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('movies.index', array_filter(['search' => request('search')])) }}"
       class="genre-chip {{ request('genre') ? '' : 'active' }}"
       style="--chip-color: #6366f1;">
        <i class="bi bi-grid-fill"></i> All
    </a>
    @foreach($genres as $genre)
        <a href="{{ route('movies.index', array_filter(['search' => request('search'), 'genre' => $genre->id])) }}"
           class="genre-chip {{ request('genre') == $genre->id ? 'active' : '' }}"
           style="--chip-color: {{ $colorFor($genre->name) }};">
            <i class="bi {{ $iconFor($genre->name) }}"></i> {{ $genre->name }}
        </a>
    @endforeach
</div>

{{-- Movie cards --}}
<div class="row g-4">
    @forelse($movies as $movie)
        @php
            $color = $colorFor($movie->genre->name);
            $icon = $iconFor($movie->genre->name);
        @endphp
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card movie-card shadow-sm h-100">
                <div class="poster-box" style="background: linear-gradient(135deg, {{ $color }}, {{ $color }}99);">
                    @if($movie->poster_url)
                        <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}">
                    @else
                        <i class="bi {{ $icon }} poster-bg-icon"></i>
                        <i class="bi {{ $icon }} poster-icon"></i>
                    @endif

                    <span class="poster-badge">
                        <i class="bi {{ $icon }}"></i> {{ $movie->genre->name }}
                    </span>

                    <span class="poster-rating">
                        <i class="bi bi-star-fill text-warning"></i>
                        {{ $movie->reviews_avg_rating ? number_format($movie->reviews_avg_rating, 1) : 'New' }}
                    </span>
                </div>

                <div class="card-body">
                    <h6 class="fw-bold mb-2">{{ $movie->title }}</h6>

                    <div class="small text-muted mb-1">
                        <i class="bi bi-person-video3" style="color: {{ $color }};"></i> {{ $movie->director }}
                    </div>
                    <div class="small text-muted mb-2">
                        <i class="bi bi-calendar-event" style="color: {{ $color }};"></i> {{ $movie->release_year }}
                        <span class="mx-1">|</span>
                        <i class="bi bi-clock" style="color: {{ $color }};"></i> {{ $movie->duration_minutes }} min
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-warning small">{{ $stars($movie->reviews_avg_rating ?? 0) }}</span>
                        <span class="small text-muted">
                            <i class="bi bi-chat-dots-fill"></i> {{ $movie->reviews_count }}
                        </span>
                    </div>
                </div>

                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                    <a href="{{ route('movies.show', $movie) }}" class="btn w-100 text-white"
                       style="background-color: {{ $color }};">
                        <i class="bi bi-play-circle-fill"></i> View movie
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center text-muted py-5">
                <i class="bi bi-emoji-frown fs-1"></i>
                <h5 class="mt-2">No movies found</h5>
                <a href="{{ route('movies.index') }}" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="bi bi-arrow-counterclockwise"></i> Clear filters
                </a>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $movies->links('pagination::bootstrap-5') }}
</div>
@endsection
