<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\DashboardDispatcherController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::redirect('/', '/login'); // Langsung arahkan ke halaman login

// =======================================================
// 1. ROUTE PINTU MASUK UTAMA (DISPATCHER)
// =======================================================
Route::get('/dashboard', [DashboardDispatcherController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// =======================================================
// 2. GROUP ROUTE KHUSUS SUPER ADMIN
// =======================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // URL: /admin/dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Jurusan
    Route::get('/jurusan', [App\Http\Controllers\Admin\MajorController::class, 'index'])->name('majors.index');
    Route::post('/jurusan', [App\Http\Controllers\Admin\MajorController::class, 'store'])->name('majors.store');

    Route::get('/jurusan/{id}/edit', [App\Http\Controllers\Admin\MajorController::class, 'edit'])->name('majors.edit');
    Route::put('/jurusan/{id}', [App\Http\Controllers\Admin\MajorController::class, 'update'])->name('majors.update');

    Route::delete('/jurusan/{id}', [App\Http\Controllers\Admin\MajorController::class, 'destroy'])->name('majors.destroy');

    // Manajemen Kaprodi
    Route::get('/kaprodi', [App\Http\Controllers\Admin\KaprodiController::class, 'index'])->name('kaprodi.index');
    Route::post('/kaprodi', [App\Http\Controllers\Admin\KaprodiController::class, 'store'])->name('kaprodi.store');
    Route::get('/kaprodi/{id}/edit', [App\Http\Controllers\Admin\KaprodiController::class, 'edit'])->name('kaprodi.edit');
    Route::put('/kaprodi/{id}', [App\Http\Controllers\Admin\KaprodiController::class, 'update'])->name('kaprodi.update');
    Route::delete('/kaprodi/{id}', [App\Http\Controllers\Admin\KaprodiController::class, 'destroy'])->name('kaprodi.destroy');

    // Manajemen Akun Siswa 
    Route::get('/siswa', [App\Http\Controllers\Admin\StudentController::class, 'index'])->name('students.index');
    Route::get('/siswa/tambah', [App\Http\Controllers\Admin\StudentController::class, 'create'])->name('students.create');
    Route::post('/siswa', [App\Http\Controllers\Admin\StudentController::class, 'store'])->name('students.store');
    Route::get('/siswa/{id}', [App\Http\Controllers\Admin\StudentController::class, 'show'])->name('students.show');
    Route::delete('/siswa/{id}', [App\Http\Controllers\Admin\StudentController::class, 'destroy'])->name('students.destroy');
    Route::post('/siswa/{id}/impersonate', [App\Http\Controllers\Admin\StudentController::class, 'impersonate'])->name('students.impersonate');
    Route::put('/siswa/{id}/reset-password', [App\Http\Controllers\Admin\StudentController::class, 'resetPassword'])->name('students.reset-password');
    Route::put('/siswa/{student_id}/reset-journal/{journal_id}', [App\Http\Controllers\Admin\StudentController::class, 'resetJournal'])->name('students.reset-journal');
    Route::put('/siswa/{student_id}/reset-monitoring/{journal_id}', [App\Http\Controllers\Admin\StudentController::class, 'resetMonitoring'])->name('students.reset-monitoring');
    Route::put('/siswa/{student_id}/reset-final-assessment/{journal_id}', [App\Http\Controllers\Admin\StudentController::class, 'resetFinalAssessment'])->name('students.reset-final-assessment');
    Route::put('/siswa/{student_id}/reset-weekly-approvals/{journal_id}', [App\Http\Controllers\Admin\StudentController::class, 'resetWeeklyApprovals'])->name('students.reset-weekly-approvals');
    
    // Rejection & Deletion Routes
    Route::post('/siswa/reject-weekly-approval/{id}', [App\Http\Controllers\Admin\StudentController::class, 'rejectWeeklyApproval'])->name('students.reject-weekly-approval');
    Route::delete('/siswa/weekly-approval/{id}', [App\Http\Controllers\Admin\StudentController::class, 'destroyWeeklyApproval'])->name('students.destroy-weekly-approval');
    Route::post('/siswa/reject-final-assessment/{id}', [App\Http\Controllers\Admin\StudentController::class, 'rejectFinalAssessment'])->name('students.reject-final-assessment');
    Route::post('/siswa/reject-monitoring/{id}', [App\Http\Controllers\Admin\StudentController::class, 'rejectMonitoring'])->name('students.reject-monitoring');
    // Manajemen Template Word
    Route::get('/template', [App\Http\Controllers\Admin\TemplateController::class, 'index'])->name('templates.index');
    Route::post('/template', [App\Http\Controllers\Admin\TemplateController::class, 'store'])->name('templates.store');
    Route::put('/template/{id}', [App\Http\Controllers\Admin\TemplateController::class, 'update'])->name('templates.update');
    Route::put('/template/{id}/activate', [App\Http\Controllers\Admin\TemplateController::class, 'activate'])->name('templates.activate');
    Route::delete('/template/{id}', [App\Http\Controllers\Admin\TemplateController::class, 'destroy'])->name('templates.destroy');

    // Manajemen Master Penilaian per Jurusan
    Route::get('/penilaian', [App\Http\Controllers\Admin\AssessmentController::class, 'index'])->name('assessments.index');
    Route::post('/penilaian', [App\Http\Controllers\Admin\AssessmentController::class, 'store'])->name('assessments.store');

    // 2 Baris baru untuk Edit:
    Route::get('/penilaian/{id}/edit', [App\Http\Controllers\Admin\AssessmentController::class, 'edit'])->name('assessments.edit');
    Route::put('/penilaian/{id}', [App\Http\Controllers\Admin\AssessmentController::class, 'update'])->name('assessments.update');

    Route::delete('/penilaian/{id}', [App\Http\Controllers\Admin\AssessmentController::class, 'destroy'])->name('assessments.destroy');

    // Validasi Bukti Siswa (Live Camera & TTD)
    Route::get('/validasi-bukti', [App\Http\Controllers\Admin\ValidationController::class, 'index'])->name('validations.index');
    Route::delete('/validasi-bukti/{type}/{id}', [App\Http\Controllers\Admin\ValidationController::class, 'destroy'])->name('validations.destroy');
});


// =======================================================
// 3. GROUP ROUTE KHUSUS KAPRODI
// =======================================================
Route::middleware(['auth', 'verified', 'role:kaprodi'])->prefix('kaprodi')->name('kaprodi.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Kaprodi\JournalController::class, 'dashboard'])->name('dashboard');
    Route::get('/validasi-bukti', [App\Http\Controllers\Kaprodi\ValidationController::class, 'index'])->name('validations.index');
    Route::delete('/validasi-bukti/{type}/{id}', [App\Http\Controllers\Kaprodi\ValidationController::class, 'destroy'])->name('validations.destroy');
    Route::get('/profil', [App\Http\Controllers\Kaprodi\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profil', [App\Http\Controllers\Kaprodi\ProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/jurnal/{id}', [App\Http\Controllers\Kaprodi\JournalController::class, 'show'])->name('journal.show');
    Route::post('/jurnal/{id}/approve', [App\Http\Controllers\Kaprodi\JournalController::class, 'approve'])->name('journal.approve');
    Route::post('/jurnal/{id}/reject', [App\Http\Controllers\Kaprodi\JournalController::class, 'reject'])->name('journal.reject');
    
    // Spesifik Reject
    Route::post('/jurnal/reject-weekly/{wa_id}', [App\Http\Controllers\Kaprodi\JournalController::class, 'rejectWeeklyApproval'])->name('journal.reject-weekly');
    Route::delete('/jurnal/weekly-approval/{wa_id}', [App\Http\Controllers\Kaprodi\JournalController::class, 'destroyWeeklyApproval'])->name('journal.destroy-weekly');
    Route::post('/jurnal/{id}/reject-final', [App\Http\Controllers\Kaprodi\JournalController::class, 'rejectFinalAssessment'])->name('journal.reject-final');
    Route::post('/jurnal/{id}/reject-monitoring', [App\Http\Controllers\Kaprodi\JournalController::class, 'rejectMonitoring'])->name('journal.reject-monitoring');

    // Spesifik Reset
    Route::put('/jurnal/{id}/reset-monitoring', [App\Http\Controllers\Kaprodi\JournalController::class, 'resetMonitoring'])->name('journal.reset-monitoring');
    Route::put('/jurnal/{id}/reset-final-assessment', [App\Http\Controllers\Kaprodi\JournalController::class, 'resetFinalAssessment'])->name('journal.reset-final-assessment');
    Route::put('/jurnal/{id}/reset-weekly-approvals', [App\Http\Controllers\Kaprodi\JournalController::class, 'resetWeeklyApprovals'])->name('journal.reset-weekly-approvals');
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

    // Batch ACC Mingguan
    Route::get('/jurnal/{id}/acc-mingguan', [JournalController::class, 'weeklyApprovalShow'])->name('journal.weekly-approval.show');
    Route::post('/jurnal/{id}/acc-mingguan', [JournalController::class, 'weeklyApprovalStore'])->name('journal.weekly-approval.store');

    // Tanda Tangan & Paraf
    Route::get('/jurnal/{id}/tanda-tangan', [JournalController::class, 'editSignatures'])->name('journal.signatures');
    Route::post('/jurnal/{id}/tanda-tangan', [JournalController::class, 'updateSignatures'])->name('journal.update-signatures');

    // Penilaian Instruktur (DUDI)
    Route::get('/jurnal/{id}/penilaian-instruktur', [App\Http\Controllers\InstructorAssessmentController::class, 'edit'])->name('journal.instructor-assessment');
    Route::post('/jurnal/{id}/penilaian-instruktur', [App\Http\Controllers\InstructorAssessmentController::class, 'update'])->name('journal.update-instructor-assessment');

    // Export Word
    Route::get('/jurnal/{id}/export', [App\Http\Controllers\ExportController::class, 'exportDocx'])->name('journal.export');

    // Ajukan ke Kaprodi
    Route::post('/jurnal/{id}/submit-kaprodi', [JournalController::class, 'submitToKaprodi'])->name('journal.submit-kaprodi');

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
    Route::post('/impersonate/leave', [App\Http\Controllers\Admin\StudentController::class, 'leaveImpersonate'])->name('impersonate.leave');
});

require __DIR__.'/auth.php';