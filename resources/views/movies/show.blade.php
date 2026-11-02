@extends('layouts.app')

@section('title', $movie->title)

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
    $genreName = $movie->genre->name;
    $color = $genreColors[$genreName] ?? $fallbackColors[crc32($genreName) % count($fallbackColors)];
    $icon = $genreIcons[$genreName] ?? 'bi-film';

    $ratingColors = [1 => '#ef4444', 2 => '#f97316', 3 => '#facc15', 4 => '#84cc16', 5 => '#22c55e'];
    $avatarColors = ['#3b82f6', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6', '#14b8a6', '#ef4444'];

    $avg = (float) ($movie->reviews_avg_rating ?? 0);
    $reviewCount = $movie->reviews->count();
    $breakdown = $movie->reviews->countBy('rating');
    $isAdmin = auth()->user()->isAdmin();

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
    .movie-hero {
        background: linear-gradient(135deg, {{ $color }}, {{ $color }}aa);
        color: #fff;
        border-radius: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .movie-hero .hero-bg-icon {
        position: absolute;
        right: -10px;
        bottom: -40px;
        font-size: 16rem;
        opacity: .13;
        transform: rotate(-12deg);
    }
    .poster-frame {
        width: 100%;
        aspect-ratio: 2 / 3;
        border-radius: 1rem;
        overflow: hidden;
        background: rgba(255, 255, 255, .18);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 .75rem 1.5rem rgba(0, 0, 0, .3);
    }
    .poster-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .poster-frame i {
        font-size: 6rem;
        opacity: .9;
    }
    .info-chip {
        background: rgba(255, 255, 255, .22);
        border-radius: 50rem;
        padding: .3rem .8rem;
        font-size: .85rem;
        backdrop-filter: blur(4px);
        display: inline-block;
    }
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: #fff;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .review-card {
        border: 0;
        border-radius: 1rem;
        transition: transform .15s ease;
    }
    .review-card:hover {
        transform: translateY(-2px);
    }
    .review-card.mine {
        border-left: 5px solid {{ $color }};
    }
    .admin-reply {
        border-left: 4px solid #3b82f6;
        border-radius: 0 .75rem .75rem 0;
    }
</style>

<a href="{{ route('movies.index') }}" class="btn btn-link px-0 mb-2 text-decoration-none">
    <i class="bi bi-arrow-left-circle-fill"></i> Back to movies
</a>

{{-- Hero --}}
<div class="movie-hero p-4 p-md-5 shadow mb-4">
    <i class="bi {{ $icon }} hero-bg-icon"></i>

    <div class="row g-4 position-relative align-items-center">
        <div class="col-md-3 col-8 mx-auto">
            <div class="poster-frame">
                @if($movie->poster_url)
                    <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}">
                @else
                    <i class="bi {{ $icon }}"></i>
                @endif
            </div>
        </div>

        <div class="col-md-9">
            <span class="info-chip mb-2">
                <i class="bi {{ $icon }}"></i> {{ $genreName }}
            </span>

            <h2 class="fw-bold mt-2 mb-3">{{ $movie->title }}</h2>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="info-chip"><i class="bi bi-person-video3"></i> {{ $movie->director }}</span>
                <span class="info-chip"><i class="bi bi-calendar-event"></i> {{ $movie->release_year }}</span>
                <span class="info-chip"><i class="bi bi-clock"></i> {{ $movie->duration_minutes }} min</span>
                <span class="info-chip"><i class="bi bi-chat-dots-fill"></i> {{ $reviewCount }} reviews</span>
            </div>

            <p class="mb-0 opacity-90">{{ $movie->synopsis }}</p>
        </div>
    </div>
</div>

{{-- Rating summary --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <div class="row align-items-center g-4">
            <div class="col-md-4 text-center">
                <div class="display-3 fw-bold" style="color: {{ $color }};">
                    {{ $avg ? number_format($avg, 1) : '-' }}
                </div>
                <div class="text-warning fs-5">{{ $stars($avg) }}</div>
                <div class="text-muted small">based on {{ $reviewCount }} reviews</div>
            </div>

            <div class="col-md-8">
                @foreach([5, 4, 3, 2, 1] as $r)
                    @php $count = (int) ($breakdown[$r] ?? 0); @endphp
                    <div class="d-flex align-items-center mb-2">
                        <span class="me-2 small fw-bold" style="width: 38px;">
                            {{ $r }} <i class="bi bi-star-fill" style="color: {{ $ratingColors[$r] }};"></i>
                        </span>
                        <div class="progress flex-grow-1" style="height: 10px;">
                            <div class="progress-bar"
                                 style="width: {{ $reviewCount ? ($count / $reviewCount) * 100 : 0 }}%; background-color: {{ $ratingColors[$r] }};"></div>
                        </div>
                        <span class="ms-2 small text-muted" style="width: 30px;">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Reviews header --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="bi bi-chat-quote-fill me-1" style="color: {{ $color }};"></i> Reviews ({{ $reviewCount }})</h5>

    @if($myReview)
        <span class="text-muted small">
            <i class="bi bi-check-circle-fill text-success"></i> You already reviewed this movie
        </span>
    @elseif(! $isAdmin)
        <button type="button" class="btn text-white" style="background-color: {{ $color }};"
                data-bs-toggle="modal" data-bs-target="#addReviewModal">
            <i class="bi bi-plus-circle-fill"></i> Add new review
        </button>
    @endif
</div>

{{-- Review list --}}
@forelse($movie->reviews as $review)
    <div class="card review-card shadow-sm mb-3 {{ $review->user_id === auth()->id() ? 'mine' : '' }}">
        <div class="card-body">
            <div class="d-flex align-items-start">
                <span class="avatar-circle me-3" style="background-color: {{ $avatarColors[$review->user_id % count($avatarColors)] }};">
                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                </span>

                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <strong>{{ $review->user->name }}</strong>
                            @if($review->user_id === auth()->id())
                                <span class="badge text-bg-success ms-1">You</span>
                            @endif
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-clock-history"></i> {{ $review->created_at->diffForHumans() }}
                        </small>
                    </div>

                    <div class="text-warning mb-1">
                        {{ $stars($review->rating) }}
                        <span class="text-body-secondary small ms-1">{{ $review->rating }} / 5</span>
                    </div>

                    <p class="mb-2">{{ $review->comment }}</p>

                    @if($review->user_id === auth()->id())
                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal" data-bs-target="#editReviewModal">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>

                            <form method="POST" action="{{ route('reviews.destroy', $review) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete your review?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($review->reply)
                        <div class="admin-reply bg-body-tertiary p-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-reply-fill text-primary me-1"></i>
                                <strong class="me-2">{{ $review->reply->admin->name }}</strong>
                                <span class="badge text-bg-primary"><i class="bi bi-patch-check-fill"></i> Admin</span>
                                <small class="text-muted ms-auto">{{ $review->reply->updated_at->diffForHumans() }}</small>
                            </div>
                            <div>{{ $review->reply->message }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-5">
        <i class="bi bi-chat-square-dots fs-1"></i>
        <h6 class="mt-2">No reviews yet</h6>
        @unless($isAdmin)
            <p class="small mb-0">Be the first to share what you think.</p>
        @endunless
    </div>
@endforelse

{{-- Add review popup --}}
@if(! $myReview && ! $isAdmin)
    <div class="modal fade" id="addReviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">
                <form method="POST" action="{{ route('reviews.store', $movie) }}">
                    @csrf

                    <div class="modal-header border-0">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle-fill" style="color: {{ $color }};"></i> Add new review
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-star-fill text-warning"></i> Rating</label>
                            <select name="rating" class="form-select @error('rating') is-invalid @enderror" required>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" @selected(old('rating', 5) == $i)>
                                        {{ $i }} {{ $i === 1 ? 'star' : 'stars' }}
                                    </option>
                                @endfor
                            </select>
                            @error('rating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-chat-text-fill"></i> Comment</label>
                            <textarea name="comment" rows="3"
                                      class="form-control @error('comment') is-invalid @enderror">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn text-white" style="background-color: {{ $color }};">
                            <i class="bi bi-send-fill"></i> Add review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('addReviewModal')).show();
            });
        </script>
    @endif
@endif

{{-- Edit review popup --}}
@if($myReview)
    <div class="modal fade" id="editReviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">
                <form method="POST" action="{{ route('reviews.store', $movie) }}">
                    @csrf

                    <div class="modal-header border-0">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square text-primary"></i> Edit your review
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-star-fill text-warning"></i> Rating</label>
                            <select name="rating" class="form-select" required>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" @selected($myReview->rating == $i)>
                                        {{ $i }} {{ $i === 1 ? 'star' : 'stars' }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-chat-text-fill"></i> Comment</label>
                            <textarea name="comment" rows="3" class="form-control">{{ $myReview->comment }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Save changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection