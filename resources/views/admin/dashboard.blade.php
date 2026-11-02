@extends('layouts.app')

@section('title', 'Admin Dashboard')

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

    $ratingColors = [1 => '#ef4444', 2 => '#f97316', 3 => '#facc15', 4 => '#84cc16', 5 => '#22c55e'];
    $ratingMap = $ratingDistribution->pluck('total', 'rating');

    $avatarColors = ['#3b82f6', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6'];

    $stars = function ($value) {
        $rounded = (int) round($value);
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $html .= '<i class="bi ' . ($i <= $rounded ? 'bi-star-fill' : 'bi-star') . '"></i> ';
        }
        return new \Illuminate\Support\HtmlString($html);
    };

    $genreLabels = $moviesPerGenre->pluck('name');
    $genreData = $moviesPerGenre->pluck('total');
    $genreChartColors = $moviesPerGenre->map(fn ($row) => $colorFor($row->name));

    $ratingChartData = collect([1, 2, 3, 4, 5])->map(fn ($r) => (int) ($ratingMap[$r] ?? 0));
    $ratingChartColors = collect([1, 2, 3, 4, 5])->map(fn ($r) => $ratingColors[$r]);
    $totalReviews = max($ratingChartData->sum(), 1);
@endphp

<style>
    .stat-card {
        position: relative;
        overflow: hidden;
        border: 0;
        color: #fff;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 .75rem 1.5rem rgba(0, 0, 0, .2) !important;
    }
    .stat-card .stat-icon {
        position: absolute;
        right: 12px;
        bottom: -8px;
        font-size: 4rem;
        opacity: .25;
    }
    .stat-card .stat-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.1;
    }
    .panel-card {
        border: 0;
        border-radius: 1rem;
    }
    .panel-title {
        font-weight: 700;
    }
    .avatar-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        color: #fff;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }
    .chart-box {
        position: relative;
        height: 280px;
    }
</style>

<div class="d-flex align-items-center mb-4">
    <i class="bi bi-speedometer2 fs-2 text-primary me-2"></i>
    <h3 class="mb-0">Admin Dashboard</h3>
</div>

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg">
        <div class="card stat-card shadow-sm rounded-4" style="background: linear-gradient(135deg, #3b82f6, #6366f1);">
            <div class="card-body">
                <div class="small opacity-75">Users</div>
                <div class="stat-number">{{ $totals['users'] }}</div>
                <i class="bi bi-people-fill stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg">
        <div class="card stat-card shadow-sm rounded-4" style="background: linear-gradient(135deg, #ec4899, #f43f5e);">
            <div class="card-body">
                <div class="small opacity-75">Movies</div>
                <div class="stat-number">{{ $totals['movies'] }}</div>
                <i class="bi bi-film stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg">
        <div class="card stat-card shadow-sm rounded-4" style="background: linear-gradient(135deg, #10b981, #14b8a6);">
            <div class="card-body">
                <div class="small opacity-75">Genres</div>
                <div class="stat-number">{{ $totals['genres'] }}</div>
                <i class="bi bi-tags-fill stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg">
        <div class="card stat-card shadow-sm rounded-4" style="background: linear-gradient(135deg, #f97316, #f59e0b);">
            <div class="card-body">
                <div class="small opacity-75">Reviews</div>
                <div class="stat-number">{{ $totals['reviews'] }}</div>
                <i class="bi bi-chat-quote-fill stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg">
        <div class="card stat-card shadow-sm rounded-4" style="background: linear-gradient(135deg, #8b5cf6, #d946ef);">
            <div class="card-body">
                <div class="small opacity-75">Average rating</div>
                <div class="stat-number">{{ $totals['average_rating'] }}</div>
                <div class="small">{{ $stars($totals['average_rating']) }}</div>
                <i class="bi bi-star-fill stat-icon"></i>
            </div>
        </div>
    </div>
</div>

{{-- Round charts --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card panel-card shadow-sm h-100">
            <div class="card-body">
                <div class="panel-title mb-3">
                    <i class="bi bi-pie-chart-fill text-primary me-1"></i> Movies per genre
                </div>
                <div class="chart-box">
                    <canvas id="genreChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card panel-card shadow-sm h-100">
            <div class="card-body">
                <div class="panel-title mb-3">
                    <i class="bi bi-star-half text-warning me-1"></i> Rating distribution
                </div>
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="chart-box" style="height: 240px;">
                            <canvas id="ratingChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        @foreach([5, 4, 3, 2, 1] as $r)
                            @php $count = (int) ($ratingMap[$r] ?? 0); @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small">
                                    <span style="color: {{ $ratingColors[$r] }};">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $r ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                    </span>
                                    <span class="fw-bold">{{ $count }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar"
                                         style="width: {{ ($count / $totalReviews) * 100 }}%; background-color: {{ $ratingColors[$r] }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reply report --}}
<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card panel-card shadow-sm h-100">
            <div class="card-body">
                <div class="panel-title mb-3">
                    <i class="bi bi-reply-all-fill text-primary me-1"></i> Reply report
                </div>

                <div class="row align-items-center">
                    <div class="col-6">
                        <div class="chart-box" style="height: 200px;">
                            <canvas id="replyChart"></canvas>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="display-5 fw-bold text-success">{{ $replyRate }}%</div>
                        <div class="small text-muted mb-3">of reviews have a reply</div>

                        <div class="d-flex align-items-center mb-1">
                            <span class="dot me-2" style="background-color: #22c55e;"></span>
                            <span class="small">Replied: <strong>{{ $repliedReviews }}</strong></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="dot me-2" style="background-color: #f97316;"></span>
                            <span class="small">Waiting: <strong>{{ $pendingReviewsCount }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card panel-card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="panel-title">
                        <i class="bi bi-hourglass-split text-warning me-1"></i> Reviews waiting for a reply
                    </div>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-reply-fill"></i> Go to reviews
                    </a>
                </div>

                @forelse($pendingReviews as $review)
                    <div class="d-flex align-items-start mb-3">
                        <span class="avatar-circle me-3" style="background-color: {{ $avatarColors[$loop->index % count($avatarColors)] }};">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </span>
                        <div class="flex-grow-1">
                            <div>
                                <span class="fw-bold">{{ $review->user->name }}</span>
                                <span class="text-muted">on</span>
                                <span class="fw-bold">{{ $review->movie->title }}</span>
                            </div>
                            <div class="text-warning small">{{ $stars($review->rating) }}</div>
                            @if($review->comment)
                                <div class="small text-muted fst-italic">"{{ $review->comment }}"</div>
                            @endif
                        </div>
                        <span class="badge text-bg-warning">Waiting</span>
                    </div>
                @empty
                    <div class="text-center text-success py-4">
                        <i class="bi bi-check-circle-fill fs-1"></i>
                        <div class="fw-bold mt-2">All reviews have a reply</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Chart(document.getElementById('replyChart'), {
            type: 'doughnut',
            data: {
                labels: ['Replied', 'Waiting'],
                datasets: [{
                    data: [{{ $repliedReviews }}, {{ $pendingReviewsCount }}],
                    backgroundColor: ['#22c55e', '#f97316'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: { legend: { display: false } }
            }
        });
    });
</script>

{{-- Top movies and reviewers --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card panel-card shadow-sm h-100">
            <div class="card-body">
                <div class="panel-title mb-3">
                    <i class="bi bi-trophy-fill text-warning me-1"></i> Top 5 rated movies
                </div>

                @php $medals = ['#facc15', '#9ca3af', '#d97706']; @endphp

                @forelse($topMovies as $index => $row)
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3 text-center" style="width: 32px;">
                            @if($index < 3)
                                <i class="bi bi-award-fill fs-4" style="color: {{ $medals[$index] }};"></i>
                            @else
                                <span class="fw-bold text-muted">#{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">{{ $row->title }}</div>
                            <span class="badge" style="background-color: {{ $colorFor($row->genre) }}; color: #fff;">
                                {{ $row->genre }}
                            </span>
                            <span class="text-warning small ms-1">{{ $stars($row->avg_rating) }}</span>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">{{ number_format((float) $row->avg_rating, 1) }}</div>
                            <div class="small text-muted">{{ $row->total_reviews }} reviews</div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No data.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card panel-card shadow-sm h-100">
            <div class="card-body">
                <div class="panel-title mb-3">
                    <i class="bi bi-fire text-danger me-1"></i> Most active reviewers
                </div>

                @forelse($topReviewers as $index => $row)
                    <div class="d-flex align-items-center mb-3">
                        <span class="avatar-circle me-3" style="background-color: {{ $avatarColors[$index % count($avatarColors)] }};">
                            {{ strtoupper(substr($row->name, 0, 1)) }}
                        </span>
                        <div class="flex-grow-1">
                            <div class="fw-bold">{{ $row->name }}</div>
                            <div class="small text-muted">{{ $row->email }}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge text-bg-primary rounded-pill">{{ $row->total_reviews }} reviews</span>
                            <div class="small text-warning">
                                <i class="bi bi-star-fill"></i> {{ number_format((float) $row->avg_rating, 1) }} given
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No data.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Latest reviews and movies without reviews --}}
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card panel-card shadow-sm h-100">
            <div class="card-body">
                <div class="panel-title mb-3">
                    <i class="bi bi-chat-heart-fill text-danger me-1"></i> Latest reviews
                </div>

                @forelse($latestReviews as $review)
                    <div class="d-flex align-items-start mb-3">
                        <span class="avatar-circle me-3" style="background-color: {{ $avatarColors[$loop->index % count($avatarColors)] }};">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </span>
                        <div class="flex-grow-1">
                            <div>
                                <span class="fw-bold">{{ $review->user->name }}</span>
                                <span class="text-muted">on</span>
                                <span class="fw-bold">{{ $review->movie->title }}</span>
                            </div>
                            <div class="text-warning small">{{ $stars($review->rating) }}</div>
                            @if($review->comment)
                                <div class="small text-muted fst-italic">"{{ $review->comment }}"</div>
                            @endif
                        </div>
                        <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                    </div>
                @empty
                    <p class="text-muted mb-0">No reviews yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card panel-card shadow-sm h-100">
            <div class="card-body">
                <div class="panel-title mb-3">
                    <i class="bi bi-exclamation-circle-fill text-warning me-1"></i> Movies without reviews
                </div>

                @forelse($moviesWithoutReviews as $movie)
                    <div class="d-flex align-items-center mb-2">
                        <span class="dot me-2" style="background-color: {{ $colorFor($movie->genre->name) }};"></span>
                        <div class="flex-grow-1">
                            <span class="fw-bold">{{ $movie->title }}</span>
                            <span class="text-muted small">({{ $movie->release_year }})</span>
                        </div>
                        <span class="badge" style="background-color: {{ $colorFor($movie->genre->name) }}; color: #fff;">
                            {{ $movie->genre->name }}
                        </span>
                    </div>
                @empty
                    <div class="text-center text-success py-4">
                        <i class="bi bi-check-circle-fill fs-1"></i>
                        <div class="fw-bold mt-2">All movies have reviews</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const cardBg = () => getComputedStyle(document.querySelector('.panel-card')).backgroundColor;
        const textColor = () => getComputedStyle(document.body).color;

        const genreChart = new Chart(document.getElementById('genreChart'), {
            type: 'doughnut',
            data: {
                labels: @json($genreLabels),
                datasets: [{
                    data: @json($genreData),
                    backgroundColor: @json($genreChartColors),
                    borderColor: cardBg(),
                    borderWidth: 3,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { color: textColor(), usePointStyle: true, pointStyle: 'circle', padding: 14 }
                    }
                }
            }
        });

        const ratingChart = new Chart(document.getElementById('ratingChart'), {
            type: 'doughnut',
            data: {
                labels: ['1 star', '2 stars', '3 stars', '4 stars', '5 stars'],
                datasets: [{
                    data: @json($ratingChartData),
                    backgroundColor: @json($ratingChartColors),
                    borderColor: cardBg(),
                    borderWidth: 3,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: { legend: { display: false } }
            }
        });

        function refreshChartTheme() {
            genreChart.options.plugins.legend.labels.color = textColor();
            genreChart.data.datasets[0].borderColor = cardBg();
            ratingChart.data.datasets[0].borderColor = cardBg();
            genreChart.update();
            ratingChart.update();
        }

        const toggle = document.getElementById('themeToggle');
        if (toggle) {
            toggle.addEventListener('click', function () {
                setTimeout(refreshChartTheme, 50);
            });
        }
    })();
</script>
@endsection