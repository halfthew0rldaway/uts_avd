<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

// Redirect root ke dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Import Excel
Route::get('/import',  [ImportController::class, 'index'])->name('import.index');
Route::post('/import', [ImportController::class, 'store'])->name('import.store');

// Export
Route::get('/export/excel', [ExportController::class, 'excel'])->name('export.excel');
Route::get('/export/pdf',   [ExportController::class, 'pdf'])->name('export.pdf');
