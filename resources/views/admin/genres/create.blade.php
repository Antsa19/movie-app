@extends('layouts.app')

@section('title', 'Add Genre')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-tag-fill fs-3 text-success me-2"></i>
                    <h4 class="mb-0">Add genre</h4>
                </div>

                <form method="POST" action="{{ route('admin.genres.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.genres.index') }}" class="btn btn-secondary">Cancel</a>
                        <button class="btn btn-success">
                            <i class="bi bi-check-lg"></i> Save genre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
