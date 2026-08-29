<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\Assessment;
use App\Models\JournalAssessment;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    // 1. Menampilkan Dashboard Siswa (Daftar Jurnal)
    public function dashboard()
    {
        $student = Auth::user()->student;
        // Ambil jurnal milik siswa ini, urutkan dari PKL 1 ke PKL 2
        $journals = Journal::where('student_id', $student->id)->orderBy('phase', 'asc')->get();
        
        return view('dashboard', compact('journals'));
    }

    public function show($id)
    {
        $student = Auth::user()->student;

        $journal = Journal::where('id', $id)
                          ->where('student_id', $student->id)
                          ->with(['dailyActivities', 'attendances', 'assessments'])
                          ->firstOrFail();

        if ($journal->phase == 2) {
            $journalFase1 = Journal::where('student_id', $student->id)->where('phase', 1)->first();
            $completedStatuses = ['COMPLETED', 'READY_TO_GENERATE', 'GENERATED'];
            if ($journalFase1 && !in_array($journalFase1->status, $completedStatuses)) {
                return redirect()->route('dashboard')->withErrors(['access' => 'Akses Ditolak! Selesaikan Jurnal Fase 1 terlebih dahulu.']);
            }
        }

        // --- LOGIKA PROGRESS TRACKER ---
        $progress = 0;
        // 1. Data PKL (20%)
        if ($journal->company_id && $journal->start_date) $progress += 20;
        // 2. Kehadiran Minimal 1 (20%)
        if ($journal->attendances->count() > 0) $progress += 20;
        // 3. Kegiatan Harian Minimal 1 (20%)
        if ($journal->dailyActivities->count() > 0) $progress += 20;
        // 4. Penilaian Instruktur (20%)
        if ($journal->assessments->count() > 0) $progress += 20;
        // 5. Tanda Tangan Lengkap (20%)
        if ($journal->student_signature && $journal->instructor_signature && $journal->instructor_paraf) $progress += 20;

        return view('student.journal.show', compact('journal', 'progress'));
    }

    // --- FORM DATA PKL ---
    public function editDataPkl($id)
    {
        $journal = Journal::where('id', $id)->where('student_id', Auth::user()->student->id)->firstOrFail();
        return view('student.journal.data-pkl', compact('journal')); // Hapus companies & teachers
    }

    public function updateDataPkl(Request $request, $id)
    {
        $journal = Journal::where('id', $id)->where('student_id', Auth::user()->student->id)->firstOrFail();

        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'teacher_name' => 'required|string|max:255',
            'instructor_name' => 'required|string|max:255',
            'instructor_position' => 'required|string|max:255',
            'instructor_phone' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $journal->update($request->all() + [
            'status' => $journal->status === 'DRAFT' ? 'IN_PROGRESS' : $journal->status
        ]);

        return redirect()->route('journal.show', $journal->id)->with('success', 'Data PKL berhasil disimpan.');
    }

    // --- FITUR GURU PEMBIMBING (DIPINDAH KE AKUN SISWA) ---
    public function editMonitoring($id)
    {
        $journal = Journal::where('id', $id)->where('student_id', Auth::user()->student->id)->firstOrFail();
        $monitoringAssessments = Assessment::where('category', 'monitoring')->orderBy('order_number')->get();
        $existingAnswers = JournalAssessment::where('journal_id', $journal->id)
                                            ->whereIn('assessment_id', $monitoringAssessments->pluck('id'))
                                            ->get()->keyBy('assessment_id');

        return view('student.journal.monitoring', compact('journal', 'monitoringAssessments', 'existingAnswers'));
    }

    public function updateMonitoring(Request $request, $id)
    {
        $journal = Journal::where('id', $id)->where('student_id', Auth::user()->student->id)->firstOrFail();
        
        foreach ($request->monitoring ?? [] as $assessmentId => $isYes) {
            JournalAssessment::updateOrCreate(
                ['journal_id' => $journal->id, 'assessment_id' => $assessmentId],
                ['is_yes' => $isYes]
            );
        }
        return redirect()->route('journal.show', $journal->id)->with('success', 'Lembar monitoring Guru berhasil disimpan.');
    }

    // Menampilkan Form Upload Tanda Tangan
    public function editSignatures($id)
    {
        $journal = Journal::where('id', $id)
                          ->where('student_id', Auth::user()->student->id)
                          ->firstOrFail();

        return view('student.journal.signatures', compact('journal'));
    }

    // Menyimpan Gambar Tanda Tangan
    public function updateSignatures(Request $request, $id)
    {
        $journal = Journal::where('id', $id)
                          ->where('student_id', Auth::user()->student->id)
                          ->firstOrFail();

        // Validasi: Harus berupa gambar (JPG/PNG), MAKSIMAL 1 MB (1024 KB)
        $request->validate([
            'student_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'parent_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'instructor_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'instructor_paraf' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ], [
            'max' => 'Ukuran gambar maksimal adalah 1 MB.',
            'image' => 'File harus berupa gambar (JPG/PNG).'
        ]);

        $data = [];
        
        // Simpan file jika ada yang diupload
        if ($request->hasFile('student_signature')) {
            $data['student_signature'] = $request->file('student_signature')->store('signatures', 'public');
        }
        if ($request->hasFile('parent_signature')) {
            $data['parent_signature'] = $request->file('parent_signature')->store('signatures', 'public');
        }
        if ($request->hasFile('instructor_signature')) {
            $data['instructor_signature'] = $request->file('instructor_signature')->store('signatures', 'public');
        }
        if ($request->hasFile('instructor_paraf')) {
            $data['instructor_paraf'] = $request->file('instructor_paraf')->store('signatures', 'public');
        }

        if(!empty($data)) {
            $journal->update($data);
        }

        return back()->with('success', 'Gambar tanda tangan / paraf berhasil disimpan.');
    }
}