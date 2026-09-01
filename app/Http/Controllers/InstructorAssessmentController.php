<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\Assessment;
use App\Models\JournalAssessment;
use Illuminate\Support\Facades\Auth;

class InstructorAssessmentController extends Controller
{
    // Menampilkan Form Penilaian untuk Instruktur
    public function edit($id)
    {
        $journal = Journal::where('id', $id)
                          ->where('student_id', Auth::user()->student->id)
                          ->firstOrFail();

        // 1. Ambil ID Jurusan milik siswa yang sedang login
        $majorId = Auth::user()->student->major_id;

        // 2. Tambahkan filter ->where('major_id', $majorId) ke setiap query
        $obsPoints = Assessment::where('major_id', $majorId)->where('category', 'observation_point')->with('children')->orderBy('order_number')->get();
        $gradeTechs = Assessment::where('major_id', $majorId)->where('category', 'grade_technical')->orderBy('order_number')->get();
        $gradeCustoms = Assessment::where('major_id', $majorId)->where('category', 'grade_custom')->orderBy('order_number')->get();
        $gradeNonTechs = Assessment::where('major_id', $majorId)->where('category', 'grade_non_technical')->orderBy('order_number')->get();

        // Ambil Jawaban/Nilai yang sudah tersimpan (jika ada)
        $existing = JournalAssessment::where('journal_id', $journal->id)->get()->keyBy('assessment_id');

        return view('student.journal.instructor-assessment', compact(
            'journal', 'obsPoints', 'gradeTechs', 'gradeCustoms', 'gradeNonTechs', 'existing'
        ));
    }

    // Menyimpan Penilaian Instruktur
    public function update(Request $request, $id)
    {
        $journal = Journal::where('id', $id)
                          ->where('student_id', Auth::user()->student->id)
                          ->firstOrFail();

        // 1. Menyimpan Observasi (Sub poin Ya/Tidak & Teks Khusus Poin 3)
        $allObsKeys = array_unique(array_merge(
            array_keys($request->obs_yes ?? []),
            array_keys($request->obs_custom_name ?? [])
        ));

        foreach ($allObsKeys as $assessmentId) {
            $isYes = $request->obs_yes[$assessmentId] ?? null;
            $description = $request->obs_custom_name[$assessmentId] ?? null;

            if ($isYes !== null || $description !== null) {
                JournalAssessment::updateOrCreate(
                    ['journal_id' => $journal->id, 'assessment_id' => $assessmentId],
                    ['is_yes' => $isYes, 'description' => $description]
                );
            }
        }

        // 2. Menyimpan Deskripsi Observasi (Diikat di Poin Utama)
        if ($request->has('obs_desc')) {
            foreach ($request->obs_desc as $assessmentId => $desc) {
                JournalAssessment::updateOrCreate(
                    ['journal_id' => $journal->id, 'assessment_id' => $assessmentId],
                    ['description' => $desc]
                );
            }
        }

        // 3. Menyimpan Nilai Angka (Teknis Tetap & Non-Teknis)
        if ($request->has('grade')) {
            foreach ($request->grade as $assessmentId => $score) {
                JournalAssessment::updateOrCreate(
                    ['journal_id' => $journal->id, 'assessment_id' => $assessmentId],
                    ['score' => $score]
                );
            }
        }

        // 4. Menyimpan Nilai Teknis Custom (Nama Kompetensi & Nilai)
        $allCustomKeys = array_unique(array_merge(
            array_keys($request->custom_grade ?? []),
            array_keys($request->custom_name ?? [])
        ));

        foreach ($allCustomKeys as $assessmentId) {
            $score = $request->custom_grade[$assessmentId] ?? null;
            $description = $request->custom_name[$assessmentId] ?? null;

            if ($score !== null || $description !== null) {
                JournalAssessment::updateOrCreate(
                    ['journal_id' => $journal->id, 'assessment_id' => $assessmentId],
                    ['score' => $score, 'description' => $description]
                );
            }
        }

        return redirect()->route('journal.show', $journal->id)->with('success', 'Penilaian Instruktur berhasil disimpan.');
    }
}