<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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

        $students = $query->get();
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
            'student_signature' => null,
            'parent_signature' => null,
            'instructor_signature' => null,
            'instructor_paraf' => null,
            'teacher_signature' => null,
            'kaprog_signature' => null,
            'instructor_live_photo' => null,
            'teacher_live_photo' => null,
            'monitoring_locked_at' => null,
            'status' => 'DRAFT',
            'grade_pdf_path' => null,
        ]);

        return back()->with('success', 'Seluruh progress Jurnal PKL Tahap '.$journal->phase.' berhasil di-reset ke kondisi awal (termasuk foto/TTD di storage).');
    }
}