<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Major;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Ambil data jurusan untuk ditampilkan di dropdown form register
        $majors = Major::all();
        return view('auth.register', compact('majors'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input: NISN harus unik di tabel students
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:50', 'unique:students,nisn'],
            'major_id' => ['required', 'exists:majors,id'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'nisn.unique' => 'NISN ini sudah terdaftar. Silakan login atau hubungi Admin jika Anda merasa tidak pernah mendaftar.'
        ]);

        // 1. Buat akun User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_STUDENT,
        ]);

        // 2. Buat profil Student dan relasikan dengan user_id & major_id
        $student = Student::create([
            'user_id' => $user->id,
            'major_id' => $request->major_id,
            'nisn' => $request->nisn,
            'name' => $request->name,
        ]);

        // 3. GENERATE 2 JURNAL KOSONG (PKL 1 & PKL 2) OTOMATIS
        \App\Models\Journal::create([
            'student_id' => $student->id,
            'phase' => 1, // Jurnal PKL 1
            'status' => 'DRAFT'
        ]);

        \App\Models\Journal::create([
            'student_id' => $student->id,
            'phase' => 2, // Jurnal PKL 2
            'status' => 'DRAFT'
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}