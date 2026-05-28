@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas to-do kamu hari ini.')

@section('content')

{{-- ======== STAT CARDS ======== --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card total">
            <div class="stat-icon"><i class="bi bi-clipboard2-data-fill"></i></div>
            <div class="stat-body">
                <p class="stat-label">Total To-Do</p>
                <h2 class="stat-value">{{ $totalTodos }}</h2>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card completed">
            <div class="stat-icon"><i class="bi bi-patch-check-fill"></i></div>
            <div class="stat-body">
                <p class="stat-label">Selesai</p>
                <h2 class="stat-value">{{ $completedTodos }}</h2>
            </div>
            @if($totalTodos > 0)
                <div class="stat-progress">
                    <div class="progress-fill" style="width: {{ round(($completedTodos / $totalTodos) * 100) }}%"></div>
                </div>
                <small class="stat-pct">{{ round(($completedTodos / $totalTodos) * 100) }}%</small>
            @endif
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card pending">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-body">
                <p class="stat-label">Pending</p>
                <h2 class="stat-value">{{ $pendingTodos }}</h2>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card overdue">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="stat-body">
                <p class="stat-label">Overdue</p>
                <h2 class="stat-value">{{ $overdueTodos }}</h2>
            </div>
        </div>
    </div>
</div>

{{-- ======== CHARTS ROW ======== --}}
<div class="row g-4 mb-4">
    {{-- Bar Chart: aktivitas 7 hari --}}
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h5 class="chart-title">Aktivitas 7 Hari Terakhir</h5>
                    <p class="chart-subtitle">To-do dibuat vs diselesaikan per hari</p>
                </div>
                <div class="chart-legend">
                    <span class="legend-dot completed"></span> Selesai
                    <span class="legend-dot pending ms-3"></span> Dibuat
                </div>
            </div>
            <div class="chart-body">
                <canvas id="activityChart" height="220"></canvas>
            </div>
        </div>
    </div>

    {{-- Donut Chart: by category --}}
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h5 class="chart-title">Distribusi Kategori</h5>
                    <p class="chart-subtitle">Breakdown per kategori</p>
                </div>
            </div>
            <div class="chart-body d-flex justify-content-center align-items-center" style="min-height:220px">
                <canvas id="categoryChart" style="max-width:220px; max-height:220px;"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ======== LISTS ROW ======== --}}
<div class="row g-4">
    {{-- Recent To-Dos --}}
    <div class="col-lg-7">
        <div class="list-card">
            <div class="list-card-header">
                <h5><i class="bi bi-clock-history me-2"></i>Terbaru Ditambahkan</h5>
                <a href="{{ route('todos.index') }}" class="btn-link-red">Lihat semua <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="todo-list">
                @forelse($recentTodos as $todo)
                    <div class="todo-item {{ $todo->is_completed ? 'done' : '' }}" id="todo-{{ $todo->id }}">
                        <button class="todo-check" onclick="toggleTodo({{ $todo->id }}, this)">
                            <i class="bi bi-{{ $todo->is_completed ? 'check-circle-fill' : 'circle' }}"></i>
                        </button>
                        <div class="todo-info">
                            <p class="todo-title">{{ $todo->title }}</p>
                            <div class="todo-meta">
                                <span class="badge-priority {{ $todo->priority_badge }}">{{ ucfirst($todo->priority) }}</span>
                                <span class="meta-cat"><i class="bi {{ $todo->category_icon }} me-1"></i>{{ ucfirst($todo->category) }}</span>
                                @if($todo->due_date)
                                    <span class="meta-date {{ $todo->due_date->isPast() && !$todo->is_completed ? 'overdue' : '' }}">
                                        <i class="bi bi-calendar-event me-1"></i>{{ $todo->due_date->format('d M Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="todo-actions">
                            <button class="action-btn edit" onclick="openEdit({{ $todo->id }}, '{{ addslashes($todo->title) }}', '{{ addslashes($todo->description ?? '') }}', '{{ $todo->priority }}', '{{ $todo->category }}', '{{ $todo->due_date?->format('Y-m-d') }}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="action-btn delete" onclick="deleteTodo({{ $todo->id }})">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>Belum ada to-do. Yuk tambahkan!</p>
                        <button class="btn-add-todo" data-bs-toggle="modal" data-bs-target="#addTodoModal">
                            <i class="bi bi-plus me-1"></i>Tambah Sekarang
                        </button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Upcoming Due --}}
    <div class="col-lg-5">
        <div class="list-card">
            <div class="list-card-header">
                <h5><i class="bi bi-alarm-fill me-2"></i>Jatuh Tempo Minggu Ini</h5>
                <a href="{{ route('todos.index', ['status' => 'pending']) }}" class="btn-link-red">Lihat semua <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="todo-list">
                @forelse($upcomingTodos as $todo)
                    <div class="todo-item upcoming" id="todo-{{ $todo->id }}">
                        <div class="due-date-badge">
                            <span class="due-day">{{ $todo->due_date->format('d') }}</span>
                            <span class="due-month">{{ $todo->due_date->translatedFormat('M') }}</span>
                        </div>
                        <div class="todo-info">
                            <p class="todo-title">{{ $todo->title }}</p>
                            <div class="todo-meta">
                                <span class="badge-priority {{ $todo->priority_badge }}">{{ ucfirst($todo->priority) }}</span>
                                @php
                                    $daysLeft = now()->diffInDays($todo->due_date, false);
                                @endphp
                                <span class="meta-days {{ $daysLeft === 0 ? 'today' : ($daysLeft < 0 ? 'overdue' : '') }}">
                                    @if($daysLeft === 0) Hari ini!
                                    @elseif($daysLeft < 0) {{ abs($daysLeft) }} hari lalu
                                    @else {{ $daysLeft }} hari lagi
                                    @endif
                                </span>
                            </div>
                        </div>
                        <button class="todo-check" onclick="toggleTodo({{ $todo->id }}, this)">
                            <i class="bi bi-circle"></i>
                        </button>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-calendar-check"></i>
                        <p>Tidak ada yang jatuh tempo minggu ini. 🎉</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Data dari Laravel
const chartLabels   = @json($chartLabels);
const chartCompleted = @json($chartCompleted);
const chartPending  = @json($chartPending);
const categoryData  = @json($categoryData);
</script>
@endpush
