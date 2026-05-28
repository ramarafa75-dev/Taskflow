@extends('layouts.app')

@section('title', 'Semua To-Do')
@section('page-title', 'Semua To-Do')
@section('page-subtitle', 'Kelola semua daftar tugasmu di sini.')

@section('content')

{{-- ======== FILTER BAR ======== --}}
<div class="filter-card mb-4">
    <form action="{{ route('todos.index') }}" method="GET" class="filter-form">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label small">Cari</label>
                <div class="search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control custom-input" placeholder="Cari judul to-do..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small">Status</label>
                <select name="status" class="form-select custom-input">
                    <option value="">Semua</option>
                    <option value="pending"   {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="overdue"   {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small">Prioritas</label>
                <select name="priority" class="form-select custom-input">
                    <option value="">Semua</option>
                    <option value="high"   {{ request('priority') === 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Sedang</option>
                    <option value="low"    {{ request('priority') === 'low' ? 'selected' : '' }}>Rendah</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small">Kategori</label>
                <select name="category" class="form-select custom-input">
                    <option value="">Semua</option>
                    <option value="personal" {{ request('category') === 'personal' ? 'selected' : '' }}>Personal</option>
                    <option value="work"     {{ request('category') === 'work' ? 'selected' : '' }}>Kerja</option>
                    <option value="shopping" {{ request('category') === 'shopping' ? 'selected' : '' }}>Belanja</option>
                    <option value="health"   {{ request('category') === 'health' ? 'selected' : '' }}>Kesehatan</option>
                    <option value="other"    {{ request('category') === 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn-submit flex-fill">
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                <a href="{{ route('todos.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- ======== ACTIONS ======== --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">
        Menampilkan <strong>{{ $todos->count() }}</strong> dari <strong>{{ $todos->total() }}</strong> to-do
    </p>
    <button class="btn btn-outline-danger btn-sm" onclick="clearCompleted()">
        <i class="bi bi-trash3 me-1"></i>Hapus yang Selesai
    </button>
</div>

{{-- ======== TODO LIST ======== --}}
<div class="list-card">
    <div class="todo-list full">
        @forelse($todos as $todo)
            <div class="todo-item {{ $todo->is_completed ? 'done' : '' }} {{ $todo->due_date && $todo->due_date->isPast() && !$todo->is_completed ? 'overdue-item' : '' }}" id="todo-{{ $todo->id }}">
                <button class="todo-check" onclick="toggleTodo({{ $todo->id }}, this)">
                    <i class="bi bi-{{ $todo->is_completed ? 'check-circle-fill' : 'circle' }}"></i>
                </button>
                <div class="todo-info">
                    <p class="todo-title">{{ $todo->title }}</p>
                    @if($todo->description)
                        <p class="todo-desc">{{ Str::limit($todo->description, 80) }}</p>
                    @endif
                    <div class="todo-meta">
                        <span class="badge-priority {{ $todo->priority_badge }}">{{ ucfirst($todo->priority) }}</span>
                        <span class="meta-cat"><i class="bi {{ $todo->category_icon }} me-1"></i>{{ ucfirst($todo->category) }}</span>
                        @if($todo->due_date)
                            <span class="meta-date {{ $todo->due_date->isPast() && !$todo->is_completed ? 'overdue' : '' }}">
                                <i class="bi bi-calendar-event me-1"></i>{{ $todo->due_date->format('d M Y') }}
                            </span>
                        @endif
                        <span class="meta-date">
                            <i class="bi bi-clock me-1"></i>{{ $todo->created_at->diffForHumans() }}
                        </span>
                        @if($todo->is_completed && $todo->completed_at)
                            <span class="meta-completed">
                                <i class="bi bi-check2 me-1"></i>Selesai {{ $todo->completed_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="todo-actions">
                    <button class="action-btn edit"
                        onclick="openEdit(
                            {{ $todo->id }},
                            '{{ addslashes($todo->title) }}',
                            '{{ addslashes($todo->description ?? '') }}',
                            '{{ $todo->priority }}',
                            '{{ $todo->category }}',
                            '{{ $todo->due_date?->format('Y-m-d') }}'
                        )">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="action-btn delete" onclick="deleteTodo({{ $todo->id }})">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="empty-state large">
                <i class="bi bi-clipboard2-x"></i>
                <h4>Tidak ada to-do ditemukan</h4>
                <p>Coba ubah filter atau tambahkan to-do baru.</p>
                <button class="btn-add-todo" data-bs-toggle="modal" data-bs-target="#addTodoModal">
                    <i class="bi bi-plus me-1"></i>Tambah To-Do
                </button>
            </div>
        @endforelse
    </div>
</div>

{{-- ======== PAGINATION ======== --}}
@if($todos->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $todos->links('pagination::bootstrap-5') }}
    </div>
@endif

@endsection
