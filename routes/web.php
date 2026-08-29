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
    Route::put('/jurnal/{id}/kegiatan/{activityId}/approve', [App\Http\Controllers\DailyActivityController::class, 'approve'])->name('journal.approve-activity');

    // Tanda Tangan & Paraf
    Route::get('/jurnal/{id}/tanda-tangan', [JournalController::class, 'editSignatures'])->name('journal.signatures');
    Route::post('/jurnal/{id}/tanda-tangan', [JournalController::class, 'updateSignatures'])->name('journal.update-signatures');

    // Penilaian Instruktur (DUDI)
    Route::get('/jurnal/{id}/penilaian-instruktur', [App\Http\Controllers\InstructorAssessmentController::class, 'edit'])->name('journal.instructor-assessment');
    Route::post('/jurnal/{id}/penilaian-instruktur', [App\Http\Controllers\InstructorAssessmentController::class, 'update'])->name('journal.update-instructor-assessment');

    // Export Word
    Route::get('/jurnal/{id}/export', [App\Http\Controllers\ExportController::class, 'exportDocx'])->name('journal.export');

    // Monitoring Guru (Diisi dari akun siswa)
    Route::get('/jurnal/{id}/monitoring', [JournalController::class, 'editMonitoring'])->name('journal.monitoring');
    Route::put('/jurnal/{id}/monitoring', [JournalController::class, 'updateMonitoring'])->name('journal.update-monitoring');
});

// Route Profil Bawaan Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';