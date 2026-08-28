<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JournalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ROUTE KHUSUS SISWA
Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    
    // Dashboard Siswa
    Route::get('/dashboard', [JournalController::class, 'dashboard'])->name('dashboard');
    
    // Menu Detail Jurnal
    Route::get('/jurnal/{id}', [JournalController::class, 'show'])->name('journal.show');
    
    // Form Pengisian Data PKL
    Route::get('/jurnal/{id}/data-pkl', [JournalController::class, 'editDataPkl'])->name('journal.data-pkl');
    Route::put('/jurnal/{id}/data-pkl', [JournalController::class, 'updateDataPkl'])->name('journal.update-data-pkl');
    
    // Kehadiran PKL
    Route::get('/jurnal/{id}/kehadiran', [App\Http\Controllers\AttendanceController::class, 'index'])->name('journal.attendance');
    Route::post('/jurnal/{id}/kehadiran', [App\Http\Controllers\AttendanceController::class, 'store'])->name('journal.store-attendance');

    // Kegiatan Harian PKL
    Route::get('/jurnal/{id}/kegiatan', [App\Http\Controllers\DailyActivityController::class, 'index'])->name('journal.activity');
    Route::post('/jurnal/{id}/kegiatan', [App\Http\Controllers\DailyActivityController::class, 'store'])->name('journal.store-activity');
});

// ROUTE KHUSUS GURU PEMBIMBING
Route::middleware(['auth', 'verified', 'role:teacher'])->group(function () {
    
    Route::get('/guru/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
    Route::get('/guru/jurnal/{id}', [TeacherController::class, 'showJournal'])->name('teacher.journal.show');
    
    // Monitoring
    Route::get('/guru/jurnal/{id}/monitoring', [TeacherController::class, 'editMonitoring'])->name('teacher.monitoring');
    Route::put('/guru/jurnal/{id}/monitoring', [TeacherController::class, 'updateMonitoring'])->name('teacher.update-monitoring');
    
});

// Route Profil Bawaan Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';