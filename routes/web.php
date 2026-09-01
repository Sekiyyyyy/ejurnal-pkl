<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JournalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::redirect('/', '/login'); // Langsung arahkan ke halaman login

// =======================================================
// 1. ROUTE PINTU MASUK UTAMA (DISPATCHER)
// =======================================================
Route::get('/dashboard', function () {
    // Jika yang login adalah Super Admin, lempar ke ruangan khusus Admin
    if (Auth::user()->role === 'super_admin') {
        return redirect()->route('admin.dashboard');
    }
    
    // Jika yang login adalah Siswa, instansiasi controller dan jalankan method dashboard
    return app()->make(JournalController::class)->dashboard();
})->middleware(['auth', 'verified'])->name('dashboard');


// =======================================================
// 2. GROUP ROUTE KHUSUS SUPER ADMIN
// =======================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // URL: /admin/dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Jurusan
    Route::get('/jurusan', [App\Http\Controllers\Admin\MajorController::class, 'index'])->name('majors.index');
    Route::post('/jurusan', [App\Http\Controllers\Admin\MajorController::class, 'store'])->name('majors.store');
    Route::delete('/jurusan/{id}', [App\Http\Controllers\Admin\MajorController::class, 'destroy'])->name('majors.destroy');

    // Manajemen Akun Siswa 
    Route::get('/siswa', [App\Http\Controllers\Admin\StudentController::class, 'index'])->name('students.index');
    Route::get('/siswa/tambah', [App\Http\Controllers\Admin\StudentController::class, 'create'])->name('students.create');
    Route::post('/siswa', [App\Http\Controllers\Admin\StudentController::class, 'store'])->name('students.store');
    Route::delete('/siswa/{id}', [App\Http\Controllers\Admin\StudentController::class, 'destroy'])->name('students.destroy');

    // Manajemen Template Word
    Route::get('/template', [App\Http\Controllers\Admin\TemplateController::class, 'index'])->name('templates.index');
    Route::post('/template', [App\Http\Controllers\Admin\TemplateController::class, 'store'])->name('templates.store');
    Route::put('/template/{id}/activate', [App\Http\Controllers\Admin\TemplateController::class, 'activate'])->name('templates.activate');
    Route::delete('/template/{id}', [App\Http\Controllers\Admin\TemplateController::class, 'destroy'])->name('templates.destroy');

    // Manajemen Master Penilaian per Jurusan
    Route::get('/penilaian', [App\Http\Controllers\Admin\AssessmentController::class, 'index'])->name('assessments.index');
    Route::post('/penilaian', [App\Http\Controllers\Admin\AssessmentController::class, 'store'])->name('assessments.store');

    // 2 Baris baru untuk Edit:
    Route::get('/penilaian/{id}/edit', [App\Http\Controllers\Admin\AssessmentController::class, 'edit'])->name('assessments.edit');
    Route::put('/penilaian/{id}', [App\Http\Controllers\Admin\AssessmentController::class, 'update'])->name('assessments.update');

    Route::delete('/penilaian/{id}', [App\Http\Controllers\Admin\AssessmentController::class, 'destroy'])->name('assessments.destroy');
});


// =======================================================
// 3. GROUP ROUTE KHUSUS SISWA
// =======================================================
Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    
    // Menu Detail Jurnal
    Route::get('/jurnal/{id}', [JournalController::class, 'show'])->name('journal.show');
    
    // Form Pengisian Data PKL
    Route::get('/jurnal/{id}/data-pkl', [JournalController::class, 'editDataPkl'])->name('journal.data-pkl');
    Route::put('/jurnal/{id}/data-pkl', [JournalController::class, 'updateDataPkl'])->name('journal.update-data-pkl');

    // Kegiatan Harian PKL
    Route::get('/jurnal/{id}/kegiatan', [App\Http\Controllers\DailyActivityController::class, 'index'])->name('journal.activity');
    Route::post('/jurnal/{id}/kegiatan', [App\Http\Controllers\DailyActivityController::class, 'store'])->name('journal.store-activity');

    // TAMBAHKAN 3 BARIS INI UNTUK FITUR EDIT & HAPUS LOGBOOK:
    Route::get('/jurnal/{id}/kegiatan/{activityId}/edit', [App\Http\Controllers\DailyActivityController::class, 'edit'])->name('journal.edit-activity');
    Route::put('/jurnal/{id}/kegiatan/{activityId}', [App\Http\Controllers\DailyActivityController::class, 'update'])->name('journal.update-activity');
    Route::delete('/jurnal/{id}/kegiatan/{activityId}', [App\Http\Controllers\DailyActivityController::class, 'destroy'])->name('journal.delete-activity');

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

    // Biodata Siswa
    Route::get('/biodata', [App\Http\Controllers\StudentProfileController::class, 'edit'])->name('student.profile.edit');
    Route::put('/biodata', [App\Http\Controllers\StudentProfileController::class, 'update'])->name('student.profile.update');
});


// =======================================================
// 4. ROUTE PROFIL BAWAAN BREEZE
// =======================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';