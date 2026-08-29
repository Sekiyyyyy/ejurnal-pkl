<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Major;
use App\Models\Journal;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Masukkan Master Asesmen
        $this->call([AssessmentSeeder::class]);

        // 2. Akun Super Admin (Untuk kelola master data nantinya)
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        // 3. Akun Siswa
        $userStudent = User::create([
            'name' => 'Siswa Testing',
            'email' => 'siswa@gmail.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_STUDENT,
        ]);
        
        $major = Major::create([
            'code' => 'TKJ',
            'name' => 'Teknik Komputer dan Jaringan',
        ]);

        $student = Student::create([
            'user_id' => $userStudent->id,
            'major_id' => $major->id,
            'nisn' => '1234567890',
            'name' => 'Siswa Testing',
        ]);

        // 4. Buat 2 Jurnal Otomatis
        // HAPUS 'teacher_id' karena sekarang menggunakan 'teacher_name' yang diisi manual di form Data PKL
        Journal::create([
            'student_id' => $student->id,
            'phase' => 1,
            'status' => 'IN_PROGRESS',
        ]);
        
        Journal::create([
            'student_id' => $student->id,
            'phase' => 2,
            'status' => 'DRAFT',
        ]);
    }
}