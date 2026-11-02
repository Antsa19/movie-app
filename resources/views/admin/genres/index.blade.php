@extends('layouts.app')

@section('title', 'Manage Genres')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <i class="bi bi-tags-fill fs-2 text-success me-2"></i>
        <h3 class="mb-0">Genres</h3>
    </div>
    <a href="{{ route('admin.genres.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg"></i> Add genre
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th class="text-center">Movies</th>
                    <th class="text-end" style="width: 200px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($genres as $genre)
                    <tr>
                        <td>{{ $genres->firstItem() + $loop->index }}</td>
                        <td class="fw-bold">{{ $genre->name }}</td>
                        <td class="text-muted">{{ $genre->description }}</td>
                        <td class="text-center">
                            <span class="badge text-bg-primary rounded-pill">{{ $genre->movies_count }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.genres.edit', $genre) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.genres.destroy', $genre) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this genre? All its movies and their reviews will also be deleted.')">
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
                        <td colspan="5" class="text-center text-muted py-4">No genres yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $genres->links('pagination::bootstrap-5') }}
</div>
@endsection
