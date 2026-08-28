<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\Company;
use App\Models\Teacher;
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

    // 2. Menampilkan Menu Utama dari Satu Jurnal
    public function show($id)
    {
        // Validasi Keamanan: Pastikan jurnal ini milik siswa yang sedang login
        $journal = Journal::where('id', $id)
                          ->where('student_id', Auth::user()->student->id)
                          ->firstOrFail();

        return view('student.journal.show', compact('journal'));
    }

    // 3. Menampilkan Form Data PKL
    public function editDataPkl($id)
    {
        $journal = Journal::where('id', $id)
                          ->where('student_id', Auth::user()->student->id)
                          ->firstOrFail();
                          
        $companies = Company::all(); // Master Tempat PKL
        $teachers = Teacher::all();  // Master Guru Pembimbing

        return view('student.journal.data-pkl', compact('journal', 'companies', 'teachers'));
    }

    // 4. Menyimpan Progress Data PKL
    public function updateDataPkl(Request $request, $id)
    {
        $journal = Journal::where('id', $id)
                          ->where('student_id', Auth::user()->student->id)
                          ->firstOrFail();

        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'instructor_name' => 'nullable|string|max:255',
            'instructor_position' => 'nullable|string|max:255',
            'instructor_phone' => 'nullable|string|max:20',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $journal->update([
            'company_id' => $request->company_id,
            'teacher_id' => $request->teacher_id,
            'instructor_name' => $request->instructor_name,
            'instructor_position' => $request->instructor_position,
            'instructor_phone' => $request->instructor_phone,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            // Ubah status jadi IN_PROGRESS jika sebelumnya DRAFT
            'status' => $journal->status === 'DRAFT' ? 'IN_PROGRESS' : $journal->status,
        ]);

        return redirect()->route('journal.show', $journal->id)->with('success', 'Progress Data PKL berhasil disimpan.');
    }
}