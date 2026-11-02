@extends('layouts.app')

@section('title', 'Manage Reviews')

@section('content')
<div class="d-flex align-items-center mb-3">
    <i class="bi bi-chat-quote-fill fs-2 text-warning me-2"></i>
    <h3 class="mb-0">Manage Reviews</h3>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>User</th>
                    <th>Movie</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Admin reply</th>
                    <th class="text-end" style="width: 260px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td>{{ $reviews->firstItem() + $loop->index }}</td>
                        <td class="fw-bold">{{ $review->user->name }}</td>
                        <td>{{ $review->movie->title }}</td>
                        <td class="text-warning text-nowrap">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </td>
                        <td class="text-muted">{{ \Illuminate\Support\Str::limit($review->comment, 50) }}</td>
                        <td>
                            @if($review->reply)
                                <span class="badge text-bg-success mb-1">
                                    <i class="bi bi-reply-fill"></i> Replied
                                </span>
                                <div class="small text-muted">
                                    {{ \Illuminate\Support\Str::limit($review->reply->message, 50) }}
                                </div>
                            @else
                                <span class="text-muted small">No reply yet</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}">
                                <i class="bi bi-reply-fill"></i>
                                {{ $review->reply ? 'Edit reply' : 'Reply' }}
                            </button>

                            @if($review->reply)
                                <form method="POST" action="{{ route('admin.replies.destroy', $review->reply) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this reply?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-secondary" title="Delete reply">
                                        <i class="bi bi-x-circle"></i> Reply
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this review? Its reply will also be deleted.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete review">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No reviews yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $reviews->links('pagination::bootstrap-5') }}
</div>

@foreach($reviews as $review)
    <div class="modal fade" id="replyModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.reviews.reply.store', $review) }}">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $review->reply ? 'Edit reply' : 'Reply to review' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="border rounded-3 p-3 mb-3 bg-body-tertiary">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $review->user->name }}</strong>
                                <span class="text-warning text-nowrap">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </span>
                            </div>
                            <div class="small text-muted mb-1">on {{ $review->movie->title }}</div>
                            <div>{{ $review->comment ?: 'No comment.' }}</div>
                        </div>

                        <label class="form-label">Your reply</label>
                        <textarea name="message" rows="4" class="form-control" maxlength="1000" required>{{ $review->reply->message ?? '' }}</textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary">
                            <i class="bi bi-send-fill"></i> {{ $review->reply ? 'Save changes' : 'Send reply' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
