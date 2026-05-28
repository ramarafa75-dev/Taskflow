<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TodoController::class, 'dashboard'])->name('dashboard');

Route::prefix('todos')->name('todos.')->group(function () {
    Route::get('/',                       [TodoController::class, 'index'])->name('index');
    Route::post('/',                      [TodoController::class, 'store'])->name('store');
    Route::put('/{todo}',                 [TodoController::class, 'update'])->name('update');
    Route::patch('/{todo}/toggle',        [TodoController::class, 'toggle'])->name('toggle');
    Route::delete('/{todo}',              [TodoController::class, 'destroy'])->name('destroy');
    Route::delete('/action/clear-completed', [TodoController::class, 'clearCompleted'])->name('clearCompleted');
});
