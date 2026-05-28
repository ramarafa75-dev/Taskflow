<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TaskFlow') — To-Do App</title>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- ========== SIDEBAR ========== --}}
<div class="app-wrapper">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="bi bi-check2-all"></i>
            </div>
            <span class="brand-name">TaskFlow</span>
        </div>

        <nav class="sidebar-nav">
            <p class="nav-label">MENU</p>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('todos.index') }}" class="nav-item {{ request()->routeIs('todos.index') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i>
                <span>Semua To-Do</span>
            </a>
            <a href="{{ route('todos.index', ['status' => 'pending']) }}" class="nav-item {{ request()->get('status') === 'pending' ? 'active' : '' }}">
                <i class="bi bi-hourglass-split"></i>
                <span>Belum Selesai</span>
                @php $pendingCount = \App\Models\Todo::pending()->count() @endphp
                @if($pendingCount > 0)
                    <span class="badge-count">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('todos.index', ['status' => 'completed']) }}" class="nav-item {{ request()->get('status') === 'completed' ? 'active' : '' }}">
                <i class="bi bi-check-circle-fill"></i>
                <span>Selesai</span>
            </a>
            <a href="{{ route('todos.index', ['status' => 'overdue']) }}" class="nav-item {{ request()->get('status') === 'overdue' ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>Overdue</span>
                @php $overdueCount = \App\Models\Todo::overdue()->count() @endphp
                @if($overdueCount > 0)
                    <span class="badge-count danger">{{ $overdueCount }}</span>
                @endif
            </a>
        </nav>

        <div class="sidebar-footer">
            <button class="btn-add-todo w-100" data-bs-toggle="modal" data-bs-target="#addTodoModal">
                <i class="bi bi-plus-circle-fill me-2"></i> Tambah To-Do
            </button>
        </div>
    </aside>

    {{-- ========== MAIN CONTENT ========== --}}
    <main class="main-content">
        {{-- Top Bar --}}
        <header class="topbar">
            <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-title">
                <h1>@yield('page-title', 'Dashboard')</h1>
                <p class="topbar-subtitle">@yield('page-subtitle', 'Selamat datang kembali!')</p>
            </div>
            <div class="topbar-actions">
                <span class="topbar-date">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
                <button class="btn-add-todo d-none d-md-flex" data-bs-toggle="modal" data-bs-target="#addTodoModal">
                    <i class="bi bi-plus-lg me-1"></i> Tambah
                </button>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert-toast success" id="flashToast">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button onclick="this.parentElement.remove()" class="toast-close"><i class="bi bi-x"></i></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert-toast error" id="flashToast">
                <i class="bi bi-x-circle-fill me-2"></i>
                {{ session('error') }}
                <button onclick="this.parentElement.remove()" class="toast-close"><i class="bi bi-x"></i></button>
            </div>
        @endif

        <div class="content-area">
            @yield('content')
        </div>
    </main>
</div>

{{-- ========== MODAL TAMBAH TO-DO ========== --}}
<div class="modal fade" id="addTodoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah To-Do Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('todos.store') }}" method="POST" id="addTodoForm">
                @csrf
                <div class="modal-body">
                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control custom-input" placeholder="Apa yang ingin kamu lakukan?" value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control custom-input" rows="3" placeholder="Tambahkan detail...">{{ old('description') }}</textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Prioritas <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select custom-input" required>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>🟢 Rendah</option>
                                <option value="medium" selected {{ old('priority') === 'medium' ? 'selected' : '' }}>🟡 Sedang</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>🔴 Tinggi</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="category" class="form-select custom-input" required>
                                <option value="personal">👤 Personal</option>
                                <option value="work">💼 Kerja</option>
                                <option value="shopping">🛒 Belanja</option>
                                <option value="health">❤️ Kesehatan</option>
                                <option value="other" selected>🏷️ Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tenggat Waktu</label>
                        <input type="date" name="due_date" class="form-control custom-input" value="{{ old('due_date') }}" min="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-plus-circle me-2"></i>Simpan To-Do
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========== MODAL EDIT TO-DO ========== --}}
<div class="modal fade" id="editTodoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit To-Do</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Judul <span class="text-danger">*</span></label>
                    <input type="text" id="editTitle" class="form-control custom-input" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea id="editDescription" class="form-control custom-input" rows="3"></textarea>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label">Prioritas</label>
                        <select id="editPriority" class="form-select custom-input">
                            <option value="low">🟢 Rendah</option>
                            <option value="medium">🟡 Sedang</option>
                            <option value="high">🔴 Tinggi</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Kategori</label>
                        <select id="editCategory" class="form-select custom-input">
                            <option value="personal">👤 Personal</option>
                            <option value="work">💼 Kerja</option>
                            <option value="shopping">🛒 Belanja</option>
                            <option value="health">❤️ Kesehatan</option>
                            <option value="other">🏷️ Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tenggat Waktu</label>
                    <input type="date" id="editDueDate" class="form-control custom-input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn-submit" id="saveEditBtn">
                    <i class="bi bi-floppy me-2"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Sidebar Overlay (mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

@stack('scripts')
</body>
</html>
