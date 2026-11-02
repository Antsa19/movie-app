@extends('layouts.app')

@section('title', 'Add Movie')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-film fs-3 text-danger me-2"></i>
                    <h4 class="mb-0">Add movie</h4>
                </div>

                <form method="POST" action="{{ route('admin.movies.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                   class="form-control @error('title') is-invalid @enderror" required autofocus>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Genre</label>
                            <select name="genre_id" class="form-select @error('genre_id') is-invalid @enderror" required>
                                <option value="">Choose genre</option>
                                @foreach($genres as $genre)
                                    <option value="{{ $genre->id }}" @selected(old('genre_id') == $genre->id)>
                                        {{ $genre->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('genre_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Director</label>
                            <input type="text" name="director" value="{{ old('director') }}"
                                   class="form-control @error('director') is-invalid @enderror" required>
                            @error('director')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Release year</label>
                            <input type="number" name="release_year" value="{{ old('release_year') }}"
                                   class="form-control @error('release_year') is-invalid @enderror" required>
                            @error('release_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Duration (min)</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}"
                                   class="form-control @error('duration_minutes') is-invalid @enderror" required>
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Poster URL (optional)</label>
                            <input type="url" name="poster_url" value="{{ old('poster_url') }}"
                                   class="form-control @error('poster_url') is-invalid @enderror"
                                   placeholder="https://...">
                            @error('poster_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Synopsis</label>
                            <textarea name="synopsis" rows="4"
                                      class="form-control @error('synopsis') is-invalid @enderror">{{ old('synopsis') }}</textarea>
                            @error('synopsis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">Cancel</a>
                        <button class="btn btn-danger">
                            <i class="bi bi-check-lg"></i> Save movie
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection