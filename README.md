# 🔴 TaskFlow — Laravel To-Do App

Aplikasi to-do list berbasis web dengan Laravel 13, Bootstrap 5, Chart.js, dan tema merah yang stylish.

---

## 🚀 Setup dengan Laravel Herd

### 1. Buat proyek Laravel baru

```bash
laravel new taskflow
cd taskflow
```

Pilih opsi: **No starter kit**, database: **SQLite** (atau MySQL sesuai preferensi)

---

### 2. Salin semua file

Copy semua file dari folder ini ke proyek Laravel kamu dengan struktur yang sama:

```
taskflow/
├── app/
│   ├── Http/Controllers/TodoController.php
│   └── Models/Todo.php
├── database/
│   ├── migrations/2024_01_01_000000_create_todos_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── TodoSeeder.php
├── resources/
│   ├── css/app.css
│   ├── js/app.js
│   └── views/
│       ├── layouts/app.blade.php
│       ├── dashboard.blade.php
│       └── todos/index.blade.php
├── routes/web.php
├── package.json
└── vite.config.js
```

---

### 3. Install dependencies PHP

```bash
composer install
```

---

### 4. Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

Untuk **SQLite** (paling mudah dengan Herd):
```bash
touch database/database.sqlite
```

Di `.env`, pastikan:
```env
DB_CONNECTION=sqlite
```

---

### 5. Jalankan migration & seeder

```bash
php artisan migrate
php artisan db:seed
```

> Seeder akan mengisi 14 dummy to-do dengan berbagai status, kategori, dan prioritas.

---

### 6. Install Node dependencies & jalankan Vite

```bash
npm install
npm run dev
```

> Buka terminal terpisah untuk `npm run dev` agar hot-reload aktif.

---

### 7. Akses aplikasi

Dengan **Laravel Herd**, aplikasi otomatis tersedia di:
```
http://taskflow.test
```

Atau gunakan:
```bash
php artisan serve
```
Lalu buka `http://localhost:8000`

---

## ✨ Fitur

| Fitur | Keterangan |
|-------|-----------|
| 📊 Dashboard | Statistik + Bar chart 7 hari + Donut chart kategori |
| ✅ Toggle selesai | Klik ikon bulat, update realtime tanpa reload |
| ➕ Tambah to-do | Modal dengan title, deskripsi, prioritas, kategori, due date |
| ✏️ Edit to-do | Modal edit dengan AJAX |
| 🗑️ Hapus to-do | Hapus satu atau hapus semua yang selesai |
| 🔍 Filter & Search | Filter by status, prioritas, kategori, dan pencarian judul |
| 📱 Responsive | Sidebar collapsible di mobile |
| ⚡ Hot Reload | `npm run dev` dengan Vite HMR |

---

## 🎨 Tech Stack

- **Laravel 13** — PHP Framework
- **Bootstrap 5.3** — CSS Framework
- **Chart.js 4** — Library grafik
- **Bootstrap Icons** — Icon set
- **Vite** — Build tool & HMR
- **Plus Jakarta Sans + DM Mono** — Typography

---

## 📁 Struktur Route

```
GET  /                          → Dashboard
GET  /todos                     → Daftar semua to-do (dengan filter)
POST /todos                     → Tambah to-do baru
PUT  /todos/{id}                → Update to-do
PATCH /todos/{id}/toggle        → Toggle selesai/pending
DELETE /todos/{id}              → Hapus to-do
DELETE /todos/action/clear-completed → Hapus semua yang selesai
```
