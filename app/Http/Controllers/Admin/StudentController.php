<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'major'])->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('filter') && $request->filter == 'expired') {
            $query->whereNotNull('jurnal_2_completed_at')
                  ->where('jurnal_2_completed_at', '<', now()->subDays(90));
        }

        $students = $query->paginate(25)->withQueryString();
        return view('admin.students.index', compact('students'));
    }

    public function show($id)
    {
        $student = Student::with([
            'user', 
            'major', 
            'journals', 
            'journals.dailyActivities', 
            'journals.assessments',
            'journals.weeklyApprovals'
        ])->findOrFail($id);
        
        return view('admin.students.show', compact('student'));
    }

    public function create()
    {
        $majors = Major::orderBy('name', 'asc')->get();
        return view('admin.students.create', compact('majors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => ['required', 'numeric', 'digits:10', 'unique:students,nisn'],
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'major_id' => 'required|exists:majors,id'
        ], [
            'nisn.unique' => 'NISN ini sudah terdaftar.',
            'email.unique' => 'Email ini sudah digunakan.'
        ]);

        // Gunakan DB Transaction agar jika salah satu gagal, keduanya dibatalkan
        DB::transaction(function () use ($request) {
            // 1. Buat akun login (tabel users)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
            ]);

            // 2. Buat profil siswa (tabel students)
            Student::create([
                'user_id' => $user->id,
                'major_id' => $request->major_id,
                'nisn' => $request->nisn,
                'name' => $request->name,
            ]);
        });

        return redirect()->route('admin.students.index')->with('success', 'Akun siswa berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        // 1. Hapus semua jurnal dan data turunannya milik siswa ini terlebih dahulu
        foreach ($student->journals as $journal) {
            // Hapus file tanda tangan dan foto dari storage
            $filesToDelete = [
                $journal->student_signature,
                $journal->parent_signature,
                $journal->instructor_signature,
                $journal->instructor_paraf,
                $journal->teacher_signature,
                $journal->kaprog_signature,
                $journal->instructor_live_photo,
                $journal->teacher_live_photo
            ];
            
            // Hapus file dari weekly approvals
            $weeklyApprovals = \App\Models\WeeklyApproval::where('journal_id', $journal->id)->get();
            foreach($weeklyApprovals as $wa) {
                $filesToDelete[] = $wa->instructor_paraf;
                $filesToDelete[] = $wa->instructor_live_photo;
            }

            foreach($filesToDelete as $file) {
                if($file) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                }
            }

            $journal->dailyActivities()->delete();
            $journal->attendances()->delete();
            $journal->assessments()->delete();
            \App\Models\WeeklyApproval::where('journal_id', $journal->id)->delete();
            $journal->delete();
        }

        // 2. Hapus data profil siswa
        $student->delete();

        // 3. Hapus akun user login siswa tersebut jika ada
        if ($student->user) {
            $student->user->delete();
        }

        return back()->with('success', 'Data siswa dan seluruh jurnal terkait berhasil dihapus.');
    }

    public function resetPassword($id)
    {
        $student = \App\Models\Student::findOrFail($id);
        
        if ($student->user) {
            // Reset password menjadi string "password123"
            $student->user->update([
                'password' => \Illuminate\Support\Facades\Hash::make('password123')
            ]);
            return back()->with('success', 'Password akun atas nama ' . $student->name . ' berhasil di-reset menjadi: password123');
        }

        return back()->withErrors(['error' => 'Siswa ini tidak memiliki akun login yang terhubung.']);
    }

    public function impersonate($id)
    {
        $student = Student::with('user')->findOrFail($id);

        if (!$student->user) {
            return back()->withErrors(['error' => 'Siswa ini belum memiliki akun pengguna yang terhubung.']);
        }

        // Simpan sesi ID admin sebelum beralih
        session([
            'impersonated_by_admin' => Auth::id(),
            'impersonated_student_id' => $student->id,
        ]);

        // Login sebagai siswa
        Auth::login($student->user);

        return redirect()->route('dashboard')->with('success', 'Anda sekarang masuk sebagai siswa: ' . $student->name);
    }

    public function leaveImpersonate()
    {
        $adminId = session('impersonated_by_admin');
        $studentId = session('impersonated_student_id');

        if (!$adminId) {
            return redirect()->route('login');
        }

        // Kembalikan auth ke akun admin
        Auth::loginUsingId($adminId);

        // Hapus sesi impersonate
        session()->forget(['impersonated_by_admin', 'impersonated_student_id']);

        if ($studentId) {
            return redirect()->route('admin.students.show', $studentId)->with('success', 'Kembali ke sesi Admin.');
        }

        return redirect()->route('admin.students.index')->with('success', 'Kembali ke sesi Admin.');
    }


    public function resetJournal($student_id, $journal_id)
    {
        $journal = \App\Models\Journal::where('student_id', $student_id)->findOrFail($journal_id);

        $filesToDelete = [
            $journal->student_signature,
            $journal->parent_signature,
            $journal->instructor_signature,
            $journal->instructor_paraf,
            $journal->teacher_signature,
            $journal->kaprog_signature,
            $journal->instructor_live_photo,
            $journal->teacher_live_photo,
        ];

        $weeklyApprovals = \App\Models\WeeklyApproval::where('journal_id', $journal->id)->get();
        foreach($weeklyApprovals as $wa) {
            $filesToDelete[] = $wa->instructor_paraf;
            $filesToDelete[] = $wa->instructor_live_photo;
        }

        foreach($filesToDelete as $file) {
            if($file) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
            }
        }

        // Hapus data turunan tapi biarkan Jurnal dan logbook hariannya?
        // Wait, the user said "reset progress, biar pas aku testing enak".
        // Menghapus data logbook (daily activities) juga akan mereset semuanya ke awal.
        $journal->dailyActivities()->delete();
        $journal->attendances()->delete();
        $journal->assessments()->delete();
        \App\Models\WeeklyApproval::where('journal_id', $journal->id)->delete();

        $journal->update([
            'company_name' => null,
            'company_address' => null,
            'start_date' => null,
            'end_date' => null,
            'instructor_name' => null,
            'teacher_name' => null,
            'student_signature' => null,
            'parent_signature' => null,
            'instructor_signature' => null,
            'instructor_paraf' => null,
            'teacher_signature' => null,
            'kaprog_signature' => null,
            'instructor_live_photo' => null,
            'teacher_live_photo' => null,
            'monitoring_locked_at' => null,
            'teacher_rejection_note' => null,
            'instructor_rejection_note' => null,
            'kaprodi_status' => 'PENDING',
            'kaprodi_rejection_note' => null,
            'status' => 'DRAFT',
            'grade_pdf_path' => null,
        ]);

        $student = $journal->student;
        if ($student) {
            if ($journal->phase == 1) {
                $student->update(['jurnal_1_completed_at' => null]);
            } else if ($journal->phase == 2) {
                $student->update(['jurnal_2_completed_at' => null]);
            }
        }

        return back()->with('success', 'Seluruh progress Jurnal PKL Tahap '.$journal->phase.' berhasil di-reset ke kondisi awal (termasuk Data PKL dan foto/TTD di storage).');
    }

    public function resetMonitoring($student_id, $journal_id)
    {
        $journal = \App\Models\Journal::where('id', $journal_id)->where('student_id', $student_id)->firstOrFail();

        // 1. Hapus penilaian observasi monitoring
        \App\Models\JournalAssessment::where('journal_id', $journal->id)
            ->whereHas('assessment', function($q) {
                $q->where('category', 'monitoring');
            })->delete();

        // 2. Hapus foto live dan tanda tangan guru dari storage secara aman
        if ($journal->teacher_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->teacher_live_photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->teacher_live_photo);
        }
        if ($journal->teacher_signature && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->teacher_signature)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->teacher_signature);
        }

        // 3. Reset kolom monitoring di jurnal
        $journal->teacher_live_photo = null;
        $journal->teacher_signature = null;
        $journal->monitoring_locked_at = null;
        $journal->teacher_rejection_note = null;

        // 4. Turunkan status jika sebelumnya COMPLETED
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

        return back()->with('success', 'Hasil Monitoring Guru untuk PKL Tahap '.$journal->phase.' berhasil di-reset. Guru pembimbing dapat mengisi ulang instrumen observasi.');
    }

    public function resetFinalAssessment($student_id, $journal_id)
    {
        $journal = \App\Models\Journal::where('id', $journal_id)->where('student_id', $student_id)->firstOrFail();

        // 1. Hapus nilai akhir instruktur
        \App\Models\JournalAssessment::where('journal_id', $journal->id)
            ->whereHas('assessment', function($q) {
                $q->where('category', '!=', 'monitoring');
            })->delete();

        // 2. Hapus foto live dan tanda tangan instruktur dari storage secara aman
        if ($journal->instructor_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->instructor_live_photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->instructor_live_photo);
        }
        if ($journal->instructor_signature && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->instructor_signature)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->instructor_signature);
        }

        // 3. Reset kolom penilaian instruktur di jurnal
        $journal->instructor_live_photo = null;
        $journal->instructor_signature = null;
        $journal->instructor_rejection_note = null;

        // 4. Turunkan status jika sebelumnya COMPLETED
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

        return back()->with('success', 'Nilai Akhir Instruktur untuk PKL Tahap '.$journal->phase.' berhasil di-reset. Instruktur dapat menginput ulang nilai dan otorisasi.');
    }

    public function resetWeeklyApprovals($student_id, $journal_id)
    {
        $journal = \App\Models\Journal::where('id', $journal_id)->where('student_id', $student_id)->firstOrFail();

        $weeklyApprovals = \App\Models\WeeklyApproval::where('journal_id', $journal->id)->get();
        foreach ($weeklyApprovals as $wa) {
            if ($wa->instructor_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($wa->instructor_live_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($wa->instructor_live_photo);
            }
            if ($wa->instructor_paraf && \Illuminate\Support\Facades\Storage::disk('public')->exists($wa->instructor_paraf)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($wa->instructor_paraf);
            }
        }

        // Un-approve all daily activities
        $journal->dailyActivities()->where('is_approved', true)->update(['is_approved' => false]);

        // Delete all weekly approvals for this journal
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

        return back()->with('success', 'Seluruh histori validasi ACC mingguan PKL Tahap '.$journal->phase.' berhasil di-reset.');
    }

    public function rejectWeeklyApproval(Request $request, $id)
    {
        $request->validate(['rejection_note' => 'required|string']);
        $wa = \App\Models\WeeklyApproval::findOrFail($id);
        
        // Delete invalid evidence safely
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
        $wa->approved_at = null; // Un-approve it
        $wa->save();

        // Also un-approve the daily activities for that week
        $startDate = $wa->journal->start_date ? \Carbon\Carbon::parse($wa->journal->start_date)->startOfWeek() : now()->startOfWeek();
        $activities = $wa->journal->dailyActivities()->where('is_approved', true)->get()->filter(function($activity) use ($wa, $startDate) {
            $activityDate = \Carbon\Carbon::parse($activity->date)->startOfWeek();
            $relativeWeek = $startDate->diffInWeeks($activityDate) + 1;
            return $relativeWeek == $wa->week_number;
        });

        foreach($activities as $activity) {
            $activity->update(['is_approved' => false]);
        }

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
            if ($journal->kaprodi_status === 'APPROVED') {
                $journal->kaprodi_status = 'PENDING';
            }
            $journal->save();
        }

        return back()->with('success', 'Bukti ACC Mingguan berhasil ditolak.');
    }

    public function destroyWeeklyApproval($id)
    {
        $validationService = app(\App\Services\ValidationService::class);
        $message = $validationService->deleteValidation('weekly', $id);
        return back()->with('success', $message);
    }

    public function rejectFinalAssessment(Request $request, $id)
    {
        $request->validate(['rejection_note' => 'required|string']);
        $journal = \App\Models\Journal::findOrFail($id);

        if ($journal->instructor_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->instructor_live_photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->instructor_live_photo);
        }
        $journal->instructor_live_photo = null;

        if ($journal->instructor_signature && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->instructor_signature)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->instructor_signature);
        }
        $journal->instructor_signature = null;

        $journal->instructor_rejection_note = $request->rejection_note;
        if ($journal->status === 'COMPLETED') {
            $journal->status = 'IN_PROGRESS'; // Unlock it
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

        return back()->with('success', 'Bukti Penilaian Akhir berhasil ditolak.');
    }

    public function rejectMonitoring(Request $request, $id)
    {
        $request->validate(['rejection_note' => 'required|string']);
        $journal = \App\Models\Journal::findOrFail($id);

        if ($journal->teacher_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->teacher_live_photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->teacher_live_photo);
        }
        $journal->teacher_live_photo = null;

        if ($journal->teacher_signature && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->teacher_signature)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->teacher_signature);
        }
        $journal->teacher_signature = null;

        $journal->teacher_rejection_note = $request->rejection_note;
        $journal->monitoring_locked_at = null; // Unlock it

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

        return back()->with('success', 'Bukti Monitoring Guru berhasil ditolak.');
    }
}