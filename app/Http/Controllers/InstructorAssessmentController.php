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

        // Ambil Data Master Penilaian berdasarkan Kategori
        $obsPoints = Assessment::where('category', 'observation_point')->with('children')->orderBy('order_number')->get();
        $gradeTechs = Assessment::where('category', 'grade_technical')->orderBy('order_number')->get();
        $gradeCustoms = Assessment::where('category', 'grade_custom')->orderBy('order_number')->get();
        $gradeNonTechs = Assessment::where('category', 'grade_non_technical')->orderBy('order_number')->get();

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

        // Menyimpan Observasi (Sub poin Ya/Tidak)
        if ($request->has('obs_yes')) {
            foreach ($request->obs_yes as $assessmentId => $value) {
                JournalAssessment::updateOrCreate(
                    ['journal_id' => $journal->id, 'assessment_id' => $assessmentId],
                    ['is_yes' => $value]
                );
            }
        }

        // Menyimpan Deskripsi Observasi (Diikat di Poin Utama)
        if ($request->has('obs_desc')) {
            foreach ($request->obs_desc as $assessmentId => $desc) {
                JournalAssessment::updateOrCreate(
                    ['journal_id' => $journal->id, 'assessment_id' => $assessmentId],
                    ['description' => $desc]
                );
            }
        }

        // Menyimpan Nilai Angka (Teknis Tetap & Non-Teknis)
        if ($request->has('grade')) {
            foreach ($request->grade as $assessmentId => $score) {
                JournalAssessment::updateOrCreate(
                    ['journal_id' => $journal->id, 'assessment_id' => $assessmentId],
                    ['score' => $score]
                );
            }
        }

        // Menyimpan Nilai Teknis Custom (Nama Kompetensi & Nilai)
        if ($request->has('custom_grade')) {
            foreach ($request->custom_grade as $assessmentId => $score) {
                // Ambil deskripsi (nama kompetensi) dari input form
                $name = $request->custom_name[$assessmentId] ?? null;
                
                JournalAssessment::updateOrCreate(
                    ['journal_id' => $journal->id, 'assessment_id' => $assessmentId],
                    ['score' => $score, 'description' => $name]
                );
            }
        }

        return redirect()->route('journal.show', $journal->id)->with('success', 'Penilaian Instruktur berhasil disimpan.');
    }
}