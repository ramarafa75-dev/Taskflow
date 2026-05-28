<?php

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TodoSeeder extends Seeder
{
    public function run(): void
    {
        $todos = [
            // Selesai
            ['title' => 'Review laporan keuangan Q3', 'priority' => 'high', 'category' => 'work', 'is_completed' => true, 'completed_at' => now()->subDays(1), 'description' => 'Review bersama tim finance'],
            ['title' => 'Beli bahan makanan minggu ini', 'priority' => 'medium', 'category' => 'shopping', 'is_completed' => true, 'completed_at' => now()->subDays(2)],
            ['title' => 'Olahraga pagi 30 menit', 'priority' => 'medium', 'category' => 'health', 'is_completed' => true, 'completed_at' => now()->subDays(1)],
            ['title' => 'Kirim proposal ke klien', 'priority' => 'high', 'category' => 'work', 'is_completed' => true, 'completed_at' => now()->subDays(3)],
            ['title' => 'Bayar tagihan listrik', 'priority' => 'high', 'category' => 'personal', 'is_completed' => true, 'completed_at' => now()->subDays(4)],

            // Pending
            ['title' => 'Persiapkan presentasi akhir tahun', 'priority' => 'high', 'category' => 'work', 'is_completed' => false, 'due_date' => now()->addDays(3)->toDateString(), 'description' => 'Slide deck + demo produk'],
            ['title' => 'Cek jadwal dokter gigi', 'priority' => 'low', 'category' => 'health', 'is_completed' => false, 'due_date' => now()->addDays(7)->toDateString()],
            ['title' => 'Renew domain website', 'priority' => 'high', 'category' => 'work', 'is_completed' => false, 'due_date' => now()->addDays(2)->toDateString()],
            ['title' => 'Belanja hadiah ulang tahun adik', 'priority' => 'medium', 'category' => 'shopping', 'is_completed' => false, 'due_date' => now()->addDays(5)->toDateString()],
            ['title' => 'Pelajari Laravel 13 features', 'priority' => 'medium', 'category' => 'personal', 'is_completed' => false, 'description' => 'Baca changelog dan coba fitur baru'],
            ['title' => 'Update CV dan portofolio', 'priority' => 'low', 'category' => 'personal', 'is_completed' => false],
            ['title' => 'Meeting dengan tim desain', 'priority' => 'high', 'category' => 'work', 'is_completed' => false, 'due_date' => now()->addDay()->toDateString()],

            // Overdue
            ['title' => 'Submit timesheet bulan lalu', 'priority' => 'high', 'category' => 'work', 'is_completed' => false, 'due_date' => now()->subDays(3)->toDateString()],
            ['title' => 'Hubungi vendor untuk penawaran', 'priority' => 'medium', 'category' => 'work', 'is_completed' => false, 'due_date' => now()->subDays(1)->toDateString()],
        ];

        foreach ($todos as $todo) {
            Todo::create($todo);
        }
    }
}
