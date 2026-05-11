<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

// Redirect root ke dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// Dashboard, Understanding & Insight
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/data-understanding', [DashboardController::class, 'understanding'])->name('understanding');
Route::get('/insight', [DashboardController::class, 'insight'])->name('insight');

// Import Excel
Route::get('/import',  fn() => redirect()->route('dashboard'));
Route::post('/import', [ImportController::class, 'store'])->name('import.store');
Route::delete('/import/reset', [ImportController::class, 'reset'])->name('import.reset');

// Export
Route::get('/export/excel', [ExportController::class, 'excel'])->name('export.excel');
Route::get('/export/pdf',   [ExportController::class, 'pdf'])->name('export.pdf');
