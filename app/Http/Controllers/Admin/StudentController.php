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
    public function index()
    {
        // Menampilkan daftar siswa beserta jurusan dan data login-nya
        $students = Student::with(['user', 'major'])->latest()->get();
        return view('admin.students.index', compact('students'));
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
            'nisn' => 'required|string|max:50|unique:students,nisn',
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
            $journal->dailyActivities()->delete();
            $journal->attendances()->delete();
            $journal->assessments()->delete();
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
}