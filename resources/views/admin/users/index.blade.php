@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
@php
    $avatarColors = ['#3b82f6', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6', '#14b8a6', '#ef4444'];
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <i class="bi bi-people-fill fs-2 text-primary me-2"></i>
        <h3 class="mb-0">Manage Users</h3>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus-fill"></i> Add user
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-center">Reviews</th>
                    <th>Joined</th>
                    <th class="text-end" style="width: 200px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $users->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-white fw-bold me-2"
                                      style="width: 36px; height: 36px; background-color: {{ $avatarColors[$user->id % count($avatarColors)] }};">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                                <span class="fw-bold">{{ $user->name }}</span>
                                @if($user->id === auth()->id())
                                    <span class="badge text-bg-success ms-2">You</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td>
                            @if($user->isAdmin())
                                <span class="badge rounded-pill" style="background-color: #8b5cf6;">
                                    <i class="bi bi-shield-fill-check"></i> Admin
                                </span>
                            @else
                                <span class="badge rounded-pill text-bg-primary">
                                    <i class="bi bi-person-fill"></i> User
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge text-bg-warning rounded-pill">{{ $user->reviews_count }}</span>
                        </td>
                        <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this user? All their reviews will also be deleted.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"
                                        @disabled($user->id === auth()->id())>
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No users yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $users->links('pagination::bootstrap-5') }}
</div>
@endsection
