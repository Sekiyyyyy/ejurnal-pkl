<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\Assessment;
use App\Models\JournalAssessment;
use App\Models\Template;
use App\Models\WeeklyApproval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JournalController extends Controller
{
    public function dashboard()
    {
        $student = Auth::user()->student;
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

        $isProfileFilled = !empty($student->class) && !empty($student->gender) && !empty($student->birth_place) && !empty($student->birth_date) && !empty($student->religion) && !empty($student->address) && !empty($student->phone) && !empty($student->parent_name) && !empty($student->parent_phone) && !empty($student->parent_address);
        $isDataPklFilled = !empty($journal->company_name) && !empty($journal->company_address) && !empty($journal->start_date) && !empty($journal->end_date) && !empty($journal->instructor_name) && !empty($journal->teacher_name);

        $isDailyFilled = false;
        $isDailyApproved = false;
        if ($journal->start_date && $journal->end_date) {
            $startDate = \Carbon\Carbon::parse($journal->start_date);
            $endDate = \Carbon\Carbon::parse($journal->end_date);
            $totalDays = $startDate->diffInDays($endDate) + 1;
            
            $activities = $journal->dailyActivities()
                                  ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                                  ->get();
                                  
            $recordedDays = $activities->count();
            $approvedDays = $activities->where('is_approved', true)->count();
            
            $isDailyFilled = ($recordedDays >= $totalDays && $totalDays > 0);
            $isDailyApproved = ($approvedDays >= $totalDays && $totalDays > 0);
        }

        $majorId = $student->major_id;

        // 1. MONITORING GURU (Hitung akurat hanya soal asli, abaikan header)
        $allMonitoring = Assessment::where('major_id', $majorId)->where('category', 'monitoring')->with('children')->get();
        $monitoringTargetIds = collect();
        foreach($allMonitoring as $m) {
            if($m->children->count() == 0) { $monitoringTargetIds->push($m->id); }
        }
        $totalMonitoringCriteria = $monitoringTargetIds->count();
        $answeredMonitoringCount = JournalAssessment::where('journal_id', $journal->id)->whereIn('assessment_id', $monitoringTargetIds)->count();
        $isMonitoringFilled = ($totalMonitoringCriteria > 0 && $answeredMonitoringCount >= $totalMonitoringCriteria);

        // 2. PENILAIAN INSTRUKTUR (Hitung akurat hanya soal asli, abaikan header)
        $allPenilaian = Assessment::where('major_id', $majorId)->where('category', '!=', 'monitoring')->with('children')->get();
        $penilaianTargetIds = collect();
        foreach($allPenilaian as $p) {
            if($p->children->count() == 0) { $penilaianTargetIds->push($p->id); }
        }
        $totalPenilaianCriteria = $penilaianTargetIds->count();
        $answeredPenilaianCount = JournalAssessment::where('journal_id', $journal->id)->whereIn('assessment_id', $penilaianTargetIds)->count();
        $isPenilaianFilled = ($totalPenilaianCriteria > 0 && $answeredPenilaianCount >= $totalPenilaianCriteria);

        $isTtdFilled = !empty($journal->student_signature) && !empty($journal->parent_signature) && !empty($journal->instructor_signature) && !empty($journal->instructor_paraf) && !empty($journal->teacher_signature) && !empty($journal->kaprog_signature);

        $hasActiveTemplate = Template::where('major_id', $student->major_id)
                                    ->where('is_active', true)
                                    ->exists();

        // Pass variabel hitungan agar View bisa menampilkan (1/6) dll
        return view('student.journal.show', compact(
            'journal', 'progress', 
            'isProfileFilled', 'isDataPklFilled', 'isDailyFilled', 'isDailyApproved',
            'isMonitoringFilled', 'isPenilaianFilled', 'isTtdFilled',
            'hasActiveTemplate',
            'answeredMonitoringCount', 'totalMonitoringCriteria',
            'answeredPenilaianCount', 'totalPenilaianCriteria'
        ));
    }

    private function calculateProgress($journal, $student)
    {
        $isProfileFilled = !empty($student->class) && !empty($student->gender) && !empty($student->birth_place) && !empty($student->birth_date) && !empty($student->religion) && !empty($student->address) && !empty($student->phone) && !empty($student->parent_name) && !empty($student->parent_phone) && !empty($student->parent_address);
        $isDataPklFilled = !empty($journal->company_name) && !empty($journal->company_address) && !empty($journal->start_date) && !empty($journal->end_date) && !empty($journal->instructor_name) && !empty($journal->teacher_name);

        $isDailyFilled = false;
        $isDailyApproved = false;
        if ($journal->start_date && $journal->end_date) {
            $startDate = \Carbon\Carbon::parse($journal->start_date);
            $endDate = \Carbon\Carbon::parse($journal->end_date);
            $totalDays = $startDate->diffInDays($endDate) + 1;
            
            $activities = $journal->dailyActivities()
                                  ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                                  ->get();
                                  
            $recordedDays = $activities->count();
            $approvedDays = $activities->where('is_approved', true)->count();
            
            $isDailyFilled = ($recordedDays >= $totalDays && $totalDays > 0);
            $isDailyApproved = ($approvedDays >= $totalDays && $totalDays > 0);
        }

        $majorId = $student->major_id;

        // 1. MONITORING GURU (Hitungan Akurat)
        $allMonitoring = Assessment::where('major_id', $majorId)->where('category', 'monitoring')->with('children')->get();
        $monitoringTargetIds = collect();
        foreach($allMonitoring as $m) {
            if($m->children->count() == 0) { $monitoringTargetIds->push($m->id); }
        }
        $answeredMonitoringCount = JournalAssessment::where('journal_id', $journal->id)->whereIn('assessment_id', $monitoringTargetIds)->count();
        $isMonitoringFilled = ($monitoringTargetIds->count() > 0 && $answeredMonitoringCount >= $monitoringTargetIds->count());

        // 2. PENILAIAN INSTRUKTUR (Hitungan Akurat)
        $allPenilaian = Assessment::where('major_id', $majorId)->where('category', '!=', 'monitoring')->with('children')->get();
        $penilaianTargetIds = collect();
        foreach($allPenilaian as $p) {
            if($p->children->count() == 0) { $penilaianTargetIds->push($p->id); }
        }
        $answeredPenilaianCount = JournalAssessment::where('journal_id', $journal->id)->whereIn('assessment_id', $penilaianTargetIds)->count();
        $isPenilaianFilled = ($penilaianTargetIds->count() > 0 && $answeredPenilaianCount >= $penilaianTargetIds->count());
        
        $isTtdFilled = !empty($journal->student_signature) && !empty($journal->parent_signature) && !empty($journal->instructor_signature) && !empty($journal->instructor_paraf) && !empty($journal->teacher_signature) && !empty($journal->kaprog_signature);

        $progress = 0;
        if ($isProfileFilled) $progress += 15;
        if ($isDataPklFilled) $progress += 15;
        if ($isDailyFilled) $progress += 20; 
        if ($isMonitoringFilled) $progress += 15;
        if ($isPenilaianFilled) $progress += 15;
        if ($isTtdFilled) $progress += 20;

        return $progress;
    }

    // --- FORM DATA PKL ---
    public function editDataPkl($id)
    {
        $journal = Journal::where('id', $id)->where('student_id', Auth::user()->student->id)->firstOrFail();
        return view('student.journal.data-pkl', compact('journal'));
    }

    public function updateDataPkl(Request $request, $id)
    {
        $journal = Journal::where('id', $id)->where('student_id', Auth::user()->student->id)->firstOrFail();
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
        return redirect()->route('journal.show', $journal->id)->with('success', 'Data PKL berhasil disimpan.');
    }

    // --- FITUR GURU PEMBIMBING ---
    public function editMonitoring($id)
    {
        $journal = Journal::where('id', $id)->where('student_id', Auth::user()->student->id)->firstOrFail();
        $majorId = Auth::user()->student->major_id;
        
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
        return redirect()->route('journal.show', $journal->id)->with('success', 'Monitoring Guru berhasil disimpan.');
    }

    // Menampilkan Form Upload Tanda Tangan
    public function editSignatures($id)
    {
        $journal = Journal::where('id', $id)->where('student_id', Auth::user()->student->id)->firstOrFail();
        return view('student.journal.signatures', compact('journal'));
    }

    // Menyimpan Gambar Tanda Tangan
    public function updateSignatures(Request $request, $id)
    {
        $journal = Journal::where('id', $id)->where('student_id', Auth::user()->student->id)->firstOrFail();
        $request->validate([
            'student_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'parent_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'instructor_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'instructor_paraf' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'teacher_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'kaprog_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);
        $data = [];
        if ($request->hasFile('student_signature')) $data['student_signature'] = $request->file('student_signature')->store('signatures', 'public');
        if ($request->hasFile('parent_signature')) $data['parent_signature'] = $request->file('parent_signature')->store('signatures', 'public');
        if ($request->hasFile('instructor_signature')) $data['instructor_signature'] = $request->file('instructor_signature')->store('signatures', 'public');
        if ($request->hasFile('instructor_paraf')) $data['instructor_paraf'] = $request->file('instructor_paraf')->store('signatures', 'public');
        if ($request->hasFile('teacher_signature')) $data['teacher_signature'] = $request->file('teacher_signature')->store('signatures', 'public');
        if ($request->hasFile('kaprog_signature')) $data['kaprog_signature'] = $request->file('kaprog_signature')->store('signatures', 'public');
        
        if(!empty($data)) { $journal->update($data); }
        return back()->with('success', 'Tanda tangan berhasil disimpan.');
    }

    // --- ACC MINGGUAN ---
    public function weeklyApprovalShow($id, Request $request)
    {
        $journal = Journal::where('id', $id)->where('student_id', Auth::user()->student->id)->firstOrFail();
        
        // Use ISO week number (1-53) and Year based on current date or selected date.
        // For simplicity, we just fetch unapproved activities and group them by week.
        $unapprovedActivities = $journal->dailyActivities()->where('is_approved', false)->orderBy('date', 'asc')->get();
        
        if ($unapprovedActivities->isEmpty()) {
            return redirect()->route('journal.show', $journal->id)->with('success', 'Semua logbook sudah di-ACC.');
        }

        // Group by week-year string (e.g., "42-2026")
        $groupedActivities = $unapprovedActivities->groupBy(function($date) {
            return \Carbon\Carbon::parse($date->date)->format('W-Y');
        });

        // Get the first group to approve
        $currentWeekGroup = $groupedActivities->keys()->first();
        $activitiesToApprove = $groupedActivities[$currentWeekGroup];
        
        $weekNumber = explode('-', $currentWeekGroup)[0];
        $year = explode('-', $currentWeekGroup)[1];

        return view('student.journal.weekly-approval', compact('journal', 'activitiesToApprove', 'weekNumber', 'year'));
    }

    public function weeklyApprovalStore(Request $request, $id)
    {
        $journal = Journal::where('id', $id)->where('student_id', Auth::user()->student->id)->firstOrFail();
        
        $request->validate([
            'week_number' => 'required|integer',
            'year' => 'required|integer',
            'signature_base64' => 'required|string',
            'live_photo_base64' => 'required|string',
        ]);

        $signaturePath = $this->saveBase64Image($request->signature_base64, 'signatures');
        $photoPath = $this->saveBase64Image($request->live_photo_base64, 'live_photos');

        // Create Weekly Approval Record
        WeeklyApproval::create([
            'journal_id' => $journal->id,
            'week_number' => $request->week_number,
            'year' => $request->year,
            'instructor_paraf' => $signaturePath,
            'instructor_live_photo' => $photoPath,
            'approved_at' => now(),
        ]);

        // Update Daily Activities for that week to is_approved = true
        $activities = $journal->dailyActivities()->where('is_approved', false)->get()->filter(function($activity) use ($request) {
            $date = \Carbon\Carbon::parse($activity->date);
            return $date->format('W') == $request->week_number && $date->format('Y') == $request->year;
        });

        foreach($activities as $activity) {
            $activity->update(['is_approved' => true]);
        }

        return redirect()->route('journal.show', $journal->id)->with('success', 'Logbook mingguan berhasil di-ACC oleh Instruktur.');
    }

    private function saveBase64Image($base64String, $folder)
    {
        $image_parts = explode(";base64,", $base64String);
        if (count($image_parts) != 2) return null;
        
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1] ?? 'png';
        
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $folder . '/' . Str::random(40) . '.' . $image_type;
        
        Storage::disk('public')->put($fileName, $image_base64);
        
        return $fileName;
    }
}