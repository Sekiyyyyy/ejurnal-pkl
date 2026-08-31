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
        
        // Hapus akun User (otomatis menghapus Student jika foreign key constraint-nya 'cascade')
        // Tapi kita hapus manual untuk memastikan data bersih
        $userId = $student->user_id;
        $student->delete(); 
        User::where('id', $userId)->delete();

        return back()->with('success', 'Akun siswa dan seluruh datanya berhasil dihapus!');
    }
}