<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TodoController extends Controller
{
    /**
     * Dashboard utama
     */
    public function dashboard()
    {
        $totalTodos     = Todo::count();
        $completedTodos = Todo::completed()->count();
        $pendingTodos   = Todo::pending()->count();
        $overdueTodos   = Todo::overdue()->count();

        // Data chart: completed per 7 hari terakhir
        $chartLabels = [];
        $chartCompleted = [];
        $chartPending = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->translatedFormat('D, d M');
            $chartCompleted[] = Todo::completed()
                ->whereDate('completed_at', $date->toDateString())
                ->count();
            $chartPending[] = Todo::pending()
                ->whereDate('created_at', $date->toDateString())
                ->count();
        }

        // Data donut chart: by category
        $categories = ['personal', 'work', 'shopping', 'health', 'other'];
        $categoryData = [];
        foreach ($categories as $cat) {
            $categoryData[$cat] = Todo::where('category', $cat)->count();
        }

        // Recent todos (5 terbaru)
        $recentTodos = Todo::latest()->take(5)->get();

        // Upcoming due (belum selesai, ada due date, dalam 7 hari ke depan)
        $upcomingTodos = Todo::pending()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('due_date')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalTodos', 'completedTodos', 'pendingTodos', 'overdueTodos',
            'chartLabels', 'chartCompleted', 'chartPending',
            'categoryData', 'recentTodos', 'upcomingTodos'
        ));
    }

    /**
     * Daftar semua todos
     */
    public function index(Request $request)
    {
        $query = Todo::query();

        // Filter
        if ($request->filled('status')) {
            if ($request->status === 'completed') $query->completed();
            if ($request->status === 'pending')   $query->pending();
            if ($request->status === 'overdue')   $query->overdue();
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sort = $request->get('sort', 'created_at');
        $dir  = $request->get('dir', 'desc');
        $query->orderBy($sort, $dir);

        $todos = $query->paginate(10)->withQueryString();

        return view('todos.index', compact('todos'));
    }

    /**
     * Simpan todo baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'priority'    => 'required|in:low,medium,high',
            'category'    => 'required|in:personal,work,shopping,health,other',
            'due_date'    => 'nullable|date|after_or_equal:today',
        ], [
            'title.required'    => 'Judul to-do wajib diisi.',
            'due_date.after_or_equal' => 'Tanggal tenggat harus hari ini atau setelahnya.',
        ]);

        Todo::create($validated);

        return redirect()->back()->with('success', 'To-do berhasil ditambahkan! 🎉');
    }

    /**
     * Update todo
     */
    public function update(Request $request, Todo $todo)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'priority'    => 'required|in:low,medium,high',
            'category'    => 'required|in:personal,work,shopping,health,other',
            'due_date'    => 'nullable|date',
        ]);

        $todo->update($validated);

        return response()->json(['success' => true, 'message' => 'To-do berhasil diperbarui!']);
    }

    /**
     * Toggle status selesai/belum
     */
    public function toggle(Todo $todo)
    {
        $todo->is_completed = ! $todo->is_completed;
        $todo->completed_at = $todo->is_completed ? now() : null;
        $todo->save();

        $msg = $todo->is_completed ? 'To-do ditandai selesai! ✅' : 'To-do dikembalikan ke pending.';

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'is_completed' => $todo->is_completed, 'message' => $msg]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Hapus todo
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'To-do berhasil dihapus!']);
        }

        return redirect()->back()->with('success', 'To-do berhasil dihapus!');
    }

    /**
     * Hapus semua yang sudah selesai
     */
    public function clearCompleted()
    {
        $count = Todo::completed()->delete();
        return response()->json(['success' => true, 'message' => "$count to-do selesai berhasil dihapus!"]);
    }
}
