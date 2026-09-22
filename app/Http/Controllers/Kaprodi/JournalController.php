<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Journal;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    public function dashboard(Request $request)
    {
        $kaprodi = Auth::user()->kaprodi;
        if (!$kaprodi) {
            abort(403, 'Akses ditolak. Anda bukan Kaprodi.');
        }

        $majorId = $kaprodi->major_id;

        $query = Journal::with(['student.user', 'student.major'])
        ->whereHas('student', function ($q) use ($majorId) {
            $q->where('major_id', $majorId);
        })
        ->where(function($q) {
            $q->whereNotNull('company_name')->orWhere('kaprodi_status', '!=', 'PENDING');
        });

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $journals = $query->orderBy('updated_at', 'desc')->paginate(10)->appends($request->query());

        // Statistics
        $totalJournals = Journal::whereHas('student', function ($q) use ($majorId) { $q->where('major_id', $majorId); })->where(function($q) { $q->whereNotNull('company_name')->orWhere('kaprodi_status', '!=', 'PENDING'); })->count();
        $waitingCount = Journal::whereHas('student', function ($q) use ($majorId) { $q->where('major_id', $majorId); })->where('kaprodi_status', 'WAITING_KAPROG')->count();
        $approvedCount = Journal::whereHas('student', function ($q) use ($majorId) { $q->where('major_id', $majorId); })->where('kaprodi_status', 'APPROVED')->count();

        return view('kaprodi.dashboard', compact('journals', 'totalJournals', 'waitingCount', 'approvedCount'));
    }

    public function show($id)
    {
        $kaprodi = Auth::user()->kaprodi;
        if (!$kaprodi) {
            abort(403);
        }

        $journal = Journal::with(['student.major', 'student.user', 'dailyActivities', 'weeklyApprovals', 'assessments.assessment'])
            ->where('id', $id)
            ->whereHas('student', function ($q) use ($kaprodi) {
                $q->where('major_id', $kaprodi->major_id);
            })
            ->firstOrFail();

        return view('kaprodi.journal_show', compact('journal'));
    }

    public function approve($id)
    {
        $kaprodi = Auth::user()->kaprodi;
        
        if (!$kaprodi->signature) {
            return back()->withErrors(['error' => 'Anda belum mengunggah tanda tangan di menu Profil. Harap unggah tanda tangan terlebih dahulu sebelum meng-ACC jurnal.']);
        }

        $journal = Journal::where('id', $id)
            ->whereHas('student', function ($q) use ($kaprodi) {
                $q->where('major_id', $kaprodi->major_id);
            })
            ->firstOrFail();

        $journal->update([
            'kaprodi_status' => 'APPROVED',
            'kaprodi_rejection_note' => null,
            'kaprog_signature' => $kaprodi->signature, // Inject signature
        ]);

        return redirect()->route('kaprodi.dashboard')->with('success', 'Jurnal berhasil di-ACC.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_note' => 'required|string',
        ]);

        $kaprodi = Auth::user()->kaprodi;
        $journal = Journal::where('id', $id)
            ->whereHas('student', function ($q) use ($kaprodi) {
                $q->where('major_id', $kaprodi->major_id);
            })
            ->firstOrFail();

        $journal->update([
            'kaprodi_status' => 'REJECTED',
            'kaprodi_rejection_note' => $request->rejection_note,
        ]);

        return redirect()->route('kaprodi.dashboard')->with('success', 'Jurnal berhasil ditolak secara keseluruhan.');
    }

    public function rejectWeeklyApproval(Request $request, $wa_id)
    {
        $request->validate(['rejection_note' => 'required|string']);
        
        // Cek wewenang
        $kaprodi = Auth::user()->kaprodi;
        $wa = \App\Models\WeeklyApproval::whereHas('journal.student', function ($q) use ($kaprodi) {
            $q->where('major_id', $kaprodi->major_id);
        })->findOrFail($wa_id);
        
        // Hapus foto live dan paraf instruktur
        if ($wa->instructor_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($wa->instructor_live_photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($wa->instructor_live_photo);
        }
        $wa->instructor_live_photo = null;

        if ($wa->instructor_paraf && \Illuminate\Support\Facades\Storage::disk('public')->exists($wa->instructor_paraf)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($wa->instructor_paraf);
        }
        $wa->instructor_paraf = null;

        $wa->rejection_note = $request->rejection_note;
        $wa->is_rejected = true;
        $wa->approved_at = null; // Un-approve
        $wa->save();

        // Un-approve harian minggu tersebut
        $startDate = $wa->journal->start_date ? \Carbon\Carbon::parse($wa->journal->start_date)->startOfWeek() : now()->startOfWeek();
        $activities = $wa->journal->dailyActivities()->where('is_approved', true)->get()->filter(function($activity) use ($wa, $startDate) {
            $activityDate = \Carbon\Carbon::parse($activity->date)->startOfWeek();
            $relativeWeek = $startDate->diffInWeeks($activityDate) + 1;
            return $relativeWeek == $wa->week_number;
        });

        foreach($activities as $activity) {
            $activity->update(['is_approved' => false]);
        }
        
        // Ubah status kaprodi & journal status
        $journal = $wa->journal;
        if ($journal) {
            if ($journal->status === 'COMPLETED') {
                $journal->status = 'IN_PROGRESS';
                $student = $journal->student;
                if ($student) {
                    if ($journal->phase == 1) {
                        $student->update(['jurnal_1_completed_at' => null]);
                    } else if ($journal->phase == 2) {
                        $student->update(['jurnal_2_completed_at' => null]);
                    }
                }
            }
            $journal->kaprodi_status = 'REJECTED';
            $journal->save();
        }

        return back()->with('success', 'Bukti ACC Mingguan berhasil ditolak.');
    }

    public function destroyWeeklyApproval($wa_id)
    {
        $kaprodi = Auth::user()->kaprodi;
        $validationService = app(\App\Services\ValidationService::class);
        $message = $validationService->deleteValidation('weekly', $wa_id, $kaprodi ? $kaprodi->major_id : null);
        return back()->with('success', $message);
    }

    public function rejectFinalAssessment(Request $request, $id)
    {
        $request->validate(['rejection_note' => 'required|string']);
        
        $kaprodi = Auth::user()->kaprodi;
        $journal = Journal::where('id', $id)->whereHas('student', function ($q) use ($kaprodi) {
            $q->where('major_id', $kaprodi->major_id);
        })->firstOrFail();

        // Hapus bukti instruktur akhir
        if ($journal->instructor_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->instructor_live_photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->instructor_live_photo);
        }
        if ($journal->instructor_signature && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->instructor_signature)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->instructor_signature);
        }

        // Hapus semua nilai akhir
        \App\Models\JournalAssessment::where('journal_id', $journal->id)
            ->whereHas('assessment', function($q) {
                $q->where('category', '!=', 'monitoring');
            })->delete();

        $journal->update([
            'instructor_rejection_note' => $request->rejection_note,
            'instructor_live_photo' => null,
            'instructor_signature' => null,
            'status' => 'IN_PROGRESS',
            'kaprodi_status' => 'REJECTED'
        ]);

        $student = $journal->student;
        if ($student) {
            if ($journal->phase == 1) {
                $student->update(['jurnal_1_completed_at' => null]);
            } else if ($journal->phase == 2) {
                $student->update(['jurnal_2_completed_at' => null]);
            }
        }

        return back()->with('success', 'Validasi Penilaian Instruktur berhasil ditolak dan nilai dihapus.');
    }

    public function rejectMonitoring(Request $request, $id)
    {
        $request->validate(['rejection_note' => 'required|string']);
        
        $kaprodi = Auth::user()->kaprodi;
        $journal = Journal::where('id', $id)->whereHas('student', function ($q) use ($kaprodi) {
            $q->where('major_id', $kaprodi->major_id);
        })->firstOrFail();

        if ($journal->teacher_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->teacher_live_photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->teacher_live_photo);
        }
        if ($journal->teacher_signature && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->teacher_signature)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->teacher_signature);
        }

        \App\Models\JournalAssessment::where('journal_id', $journal->id)
            ->whereHas('assessment', function($q) {
                $q->where('category', 'monitoring');
            })->delete();

        $updateData = [
            'teacher_rejection_note' => $request->rejection_note,
            'teacher_live_photo' => null,
            'teacher_signature' => null,
            'monitoring_locked_at' => null,
            'kaprodi_status' => 'REJECTED'
        ];

        if ($journal->status === 'COMPLETED') {
            $updateData['status'] = 'IN_PROGRESS';
            $student = $journal->student;
            if ($student) {
                if ($journal->phase == 1) {
                    $student->update(['jurnal_1_completed_at' => null]);
                } else if ($journal->phase == 2) {
                    $student->update(['jurnal_2_completed_at' => null]);
                }
            }
        }

        $journal->update($updateData);

        return back()->with('success', 'Validasi Monitoring Guru berhasil ditolak dan hasil dihapus.');
    }

    public function resetMonitoring($id)
    {
        $kaprodi = Auth::user()->kaprodi;
        $journal = Journal::where('id', $id)->whereHas('student', function ($q) use ($kaprodi) {
            $q->where('major_id', $kaprodi->major_id);
        })->firstOrFail();

        // 1. Hapus nilai observasi monitoring
        \App\Models\JournalAssessment::where('journal_id', $journal->id)
            ->whereHas('assessment', function($q) {
                $q->where('category', 'monitoring');
            })->delete();

        // 2. Hapus bukti file foto & tanda tangan dari storage
        if ($journal->teacher_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->teacher_live_photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->teacher_live_photo);
        }
        if ($journal->teacher_signature && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->teacher_signature)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->teacher_signature);
        }

        $updateData = [
            'teacher_rejection_note' => null,
            'teacher_live_photo' => null,
            'teacher_signature' => null,
            'monitoring_locked_at' => null,
            'kaprodi_status' => 'PENDING'
        ];

        if ($journal->status === 'COMPLETED') {
            $updateData['status'] = 'IN_PROGRESS';
            $student = $journal->student;
            if ($student) {
                if ($journal->phase == 1) {
                    $student->update(['jurnal_1_completed_at' => null]);
                } else if ($journal->phase == 2) {
                    $student->update(['jurnal_2_completed_at' => null]);
                }
            }
        }

        $journal->update($updateData);

        return back()->with('success', 'Hasil Monitoring Guru berhasil di-reset total.');
    }

    public function resetFinalAssessment($id)
    {
        $kaprodi = Auth::user()->kaprodi;
        $journal = Journal::where('id', $id)->whereHas('student', function ($q) use ($kaprodi) {
            $q->where('major_id', $kaprodi->major_id);
        })->firstOrFail();

        // 1. Hapus bukti instruktur akhir
        if ($journal->instructor_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->instructor_live_photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->instructor_live_photo);
        }
        if ($journal->instructor_signature && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->instructor_signature)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->instructor_signature);
        }

        // 2. Hapus semua nilai akhir
        \App\Models\JournalAssessment::where('journal_id', $journal->id)
            ->whereHas('assessment', function($q) {
                $q->where('category', '!=', 'monitoring');
            })->delete();

        $journal->update([
            'instructor_rejection_note' => null,
            'instructor_live_photo' => null,
            'instructor_signature' => null,
            'status' => 'IN_PROGRESS',
            'kaprodi_status' => 'PENDING'
        ]);

        $student = $journal->student;
        if ($student) {
            if ($journal->phase == 1) {
                $student->update(['jurnal_1_completed_at' => null]);
            } else if ($journal->phase == 2) {
                $student->update(['jurnal_2_completed_at' => null]);
            }
        }

        return back()->with('success', 'Nilai Akhir Instruktur berhasil di-reset total.');
    }

    public function resetWeeklyApprovals($id)
    {
        $kaprodi = Auth::user()->kaprodi;
        $journal = Journal::where('id', $id)->whereHas('student', function ($q) use ($kaprodi) {
            $q->where('major_id', $kaprodi->major_id);
        })->firstOrFail();

        $weeklyApprovals = \App\Models\WeeklyApproval::where('journal_id', $journal->id)->get();
        foreach ($weeklyApprovals as $wa) {
            if ($wa->instructor_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($wa->instructor_live_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($wa->instructor_live_photo);
            }
            if ($wa->instructor_paraf && \Illuminate\Support\Facades\Storage::disk('public')->exists($wa->instructor_paraf)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($wa->instructor_paraf);
            }
        }

        $journal->dailyActivities()->where('is_approved', true)->update(['is_approved' => false]);
        \App\Models\WeeklyApproval::where('journal_id', $journal->id)->delete();

        if ($journal->status === 'COMPLETED') {
            $journal->status = 'IN_PROGRESS';
            $student = $journal->student;
            if ($student) {
                if ($journal->phase == 1) {
                    $student->update(['jurnal_1_completed_at' => null]);
                } else if ($journal->phase == 2) {
                    $student->update(['jurnal_2_completed_at' => null]);
                }
            }
        }
        if ($journal->kaprodi_status === 'APPROVED') {
            $journal->kaprodi_status = 'PENDING';
        }
        $journal->save();

        return back()->with('success', 'Seluruh histori ACC mingguan berhasil di-reset.');
    }
}
