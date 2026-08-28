<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat Data Jurusan Dummy
        $major = \App\Models\Major::create([
            'code' => 'TKJ',
            'name' => 'Teknik Komputer dan Jaringan',
        ]);

        // Buat Data Siswa Dummy (Belum punya akun)
        \App\Models\Student::create([
            'major_id' => $major->id,
            'nisn' => '1234567890',
            'name' => 'Budi Santoso',
        ]);
    }
}
