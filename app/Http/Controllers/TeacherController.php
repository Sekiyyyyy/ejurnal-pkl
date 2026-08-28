<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Journal;
use App\Models\Assessment;
use App\Models\JournalAssessment;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    // 1. Dashboard Guru (Daftar Siswa Bimbingan)
    public function dashboard()
    {
        $teacher = Auth::user()->teacher;
        
        // Jika akun user teacher belum di-link ke tabel teachers, cegah error
        if (!$teacher) {
            return view('teacher.no-profile');
        }

        // Ambil semua jurnal siswa yang dibimbing oleh guru ini
        $journals = Journal::where('teacher_id', $teacher->id)
                           ->with(['student.major', 'company'])
                           ->get();

        return view('teacher.dashboard', compact('journals'));
    }

    // 2. Detail Jurnal Siswa (Menu Utama Review Guru)
    public function showJournal($id)
    {
        $teacher = Auth::user()->teacher;
        
        $journal = Journal::where('id', $id)
                          ->where('teacher_id', $teacher->id)
                          ->with(['student.major', 'company', 'attendances', 'dailyActivities'])
                          ->firstOrFail();

        return view('teacher.journal-show', compact('journal'));
    }

    // 3. Form Lembar Monitoring Guru Pembimbing
    public function editMonitoring($id)
    {
        $teacher = Auth::user()->teacher;
        
        $journal = Journal::where('id', $id)
                          ->where('teacher_id', $teacher->id)
                          ->firstOrFail();

        // Ambil master pertanyaan monitoring
        $monitoringAssessments = Assessment::where('category', 'monitoring')->orderBy('order_number')->get();

        // Ambil jawaban yang sudah pernah diisi (jika ada)
        $existingAnswers = JournalAssessment::where('journal_id', $journal->id)
                                            ->whereIn('assessment_id', $monitoringAssessments->pluck('id'))
                                            ->get()
                                            ->keyBy('assessment_id');

        return view('teacher.monitoring', compact('journal', 'monitoringAssessments', 'existingAnswers'));
    }

    // 4. Simpan Lembar Monitoring
    public function updateMonitoring(Request $request, $id)
    {
        $teacher = Auth::user()->teacher;
        
        $journal = Journal::where('id', $id)
                          ->where('teacher_id', $teacher->id)
                          ->firstOrFail();

        $monitoringAssessments = Assessment::where('category', 'monitoring')->get();

        // Validasi: Pastikan semua item monitoring diisi (true/false)
        foreach ($monitoringAssessments as $item) {
            $request->validate([
                "monitoring.{$item->id}" => 'required|boolean'
            ]);
        }

        // Simpan atau Update ke database
        foreach ($request->monitoring as $assessmentId => $isYes) {
            JournalAssessment::updateOrCreate(
                [
                    'journal_id' => $journal->id,
                    'assessment_id' => $assessmentId,
                ],
                [
                    'is_yes' => $isYes,
                ]
            );
        }

        return redirect()->route('teacher.journal.show', $journal->id)->with('success', 'Lembar monitoring berhasil disimpan.');
    }
}