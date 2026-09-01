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
                            ->with(['dailyActivities', 'assessments.assessment'])
                            ->firstOrFail();

        if ($journal->phase == 2) {
            $journalFase1 = Journal::where('student_id', $student->id)->where('phase', 1)->first();
            if ($journalFase1) {
                $progressFase1 = $this->calculateProgress($journalFase1, $student);
                if ($progressFase1 < 100) {
                    return redirect()->route('dashboard')->withErrors(['access' => 'Akses Ditolak! Selesaikan Jurnal Fase 1 hingga 100% terlebih dahulu (Saat ini baru ' . $progressFase1 . '%).']);
                }
            } else {
                return redirect()->route('dashboard')->withErrors(['access' => 'Akses Ditolak! Jurnal Fase 1 belum tersedia.']);
            }
        }

        $progress = $this->calculateProgress($journal, $student);

        // Variabel status untuk view
        $isProfileFilled = !empty($student->class) && !empty($student->gender) && !empty($student->birth_place) && !empty($student->birth_date) && !empty($student->religion) && !empty($student->address) && !empty($student->phone) && !empty($student->parent_name) && !empty($student->parent_phone) && !empty($student->parent_address);

        $isDataPklFilled = !empty($journal->company_name) && !empty($journal->company_address) && !empty($journal->start_date) && !empty($journal->end_date) && !empty($journal->instructor_name) && !empty($journal->teacher_name);

        // Pengecekan rentang tanggal harian (gabungan absensi & logbook)
        $isDailyFilled = false;
        if ($journal->start_date && $journal->end_date) {
            $startDate = \Carbon\Carbon::parse($journal->start_date);
            $endDate = \Carbon\Carbon::parse($journal->end_date);
            $totalDays = $startDate->diffInDays($endDate) + 1;
            $recordedDays = $journal->dailyActivities()
                                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                                    ->count();
            $isDailyFilled = ($recordedDays >= $totalDays && $totalDays > 0);
        }

        $isMonitoringFilled = $journal->assessments->where('assessment.category', 'monitoring')->count() > 0;
        $isPenilaianFilled = $journal->assessments->whereIn('assessment.category', ['grade_technical', 'grade_non_technical', 'grade_custom'])->count() > 0;
        
        $isTtdFilled = !empty($journal->student_signature) && !empty($journal->parent_signature) && !empty($journal->instructor_signature) && !empty($journal->instructor_paraf) && !empty($journal->teacher_signature) && !empty($journal->kaprog_signature);

        return view('student.journal.show', compact(
            'journal', 'progress', 
            'isProfileFilled', 'isDataPklFilled', 'isDailyFilled', 
            'isMonitoringFilled', 'isPenilaianFilled', 'isTtdFilled'
        ));
    }

    private function calculateProgress($journal, $student)
    {
        $isProfileFilled = !empty($student->class) && !empty($student->gender) && !empty($student->birth_place) && !empty($student->birth_date) && !empty($student->religion) && !empty($student->address) && !empty($student->phone) && !empty($student->parent_name) && !empty($student->parent_phone) && !empty($student->parent_address);

        $isDataPklFilled = !empty($journal->company_name) && !empty($journal->company_address) && !empty($journal->start_date) && !empty($journal->end_date) && !empty($journal->instructor_name) && !empty($journal->teacher_name);

        $isDailyFilled = false;
        if ($journal->start_date && $journal->end_date) {
            $startDate = \Carbon\Carbon::parse($journal->start_date);
            $endDate = \Carbon\Carbon::parse($journal->end_date);
            $totalDays = $startDate->diffInDays($endDate) + 1;
            $recordedDays = $journal->dailyActivities()
                                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                                    ->count();
            $isDailyFilled = ($recordedDays >= $totalDays && $totalDays > 0);
        }

        $isMonitoringFilled = $journal->assessments->where('assessment.category', 'monitoring')->count() > 0;
        $isPenilaianFilled = $journal->assessments->whereIn('assessment.category', ['grade_technical', 'grade_non_technical', 'grade_custom'])->count() > 0;
        
        $isTtdFilled = !empty($journal->student_signature) && !empty($journal->parent_signature) && !empty($journal->instructor_signature) && !empty($journal->instructor_paraf) && !empty($journal->teacher_signature) && !empty($journal->kaprog_signature);

        $progress = 0;
        if ($isProfileFilled) $progress += 15;
        if ($isDataPklFilled) $progress += 15;
        if ($isDailyFilled) $progress += 20; // 20% gabungan kehadiran & kegiatan
        if ($isMonitoringFilled) $progress += 15;
        if ($isPenilaianFilled) $progress += 15;
        if ($isTtdFilled) $progress += 20;

        return $progress;
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

        // Validasi dibuat fleksibel (nullable) untuk mendukung pengisian berkala
        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:255',
            'teacher_name' => 'nullable|string|max:255',
            'teacher_address' => 'nullable|string',
            'teacher_phone' => 'nullable|string|max:20',
            'instructor_name' => 'nullable|string|max:255',
            'instructor_position' => 'nullable|string|max:255',
            'instructor_address' => 'nullable|string',
            'instructor_phone' => 'nullable|string|max:20',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $journal->update($request->all() + [
            'status' => $journal->status === 'DRAFT' ? 'IN_PROGRESS' : $journal->status
        ]);

        return redirect()->route('journal.show', $journal->id)->with('success', 'Data PKL berhasil disimpan secara berkala.');
    }

    // --- FITUR GURU PEMBIMBING (DIPINDAH KE AKUN SISWA) ---
    public function editMonitoring($id)
    {
        $journal = Journal::where('id', $id)->where('student_id', Auth::user()->student->id)->firstOrFail();
        
        // 1. Ambil ID Jurusan
        $majorId = Auth::user()->student->major_id;
        
        // 2. Tambahkan filter ->where('major_id', $majorId)
        $monitoringAssessments = Assessment::where('major_id', $majorId)
                                           ->where('category', 'monitoring')
                                           ->orderBy('order_number')
                                           ->get();
                                           
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
            'teacher_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'kaprog_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ], [
            'max' => 'Ukuran gambar maksimal adalah 1 MB.',
            'image' => 'File harus berupa gambar (JPG/PNG).'
        ]);

        $data = [];
        
        // Simpan file jika ada yang diupload
        if ($request->hasFile('student_signature')) $data['student_signature'] = $request->file('student_signature')->store('signatures', 'public');
        if ($request->hasFile('parent_signature')) $data['parent_signature'] = $request->file('parent_signature')->store('signatures', 'public');
        if ($request->hasFile('instructor_signature')) $data['instructor_signature'] = $request->file('instructor_signature')->store('signatures', 'public');
        if ($request->hasFile('instructor_paraf')) $data['instructor_paraf'] = $request->file('instructor_paraf')->store('signatures', 'public');
        if ($request->hasFile('teacher_signature')) $data['teacher_signature'] = $request->file('teacher_signature')->store('signatures', 'public');
        if ($request->hasFile('kaprog_signature')) $data['kaprog_signature'] = $request->file('kaprog_signature')->store('signatures', 'public');

        if(!empty($data)) {
            $journal->update($data);
        }

        return back()->with('success', 'Gambar tanda tangan / paraf berhasil disimpan.');
    }
}