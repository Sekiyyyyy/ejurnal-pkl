<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Major;
use App\Models\Journal;
use App\Models\Template;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data untuk Card Statistik
        $totalStudents = Student::count();
        $totalMajors = Major::count();
        $totalJournals = Journal::count();
        $totalTemplates = Template::count();

        // 2. Data untuk Grafik (Menghitung jumlah siswa di setiap jurusan)
        $majors = Major::all();
        $chartLabels = [];
        $chartData = [];

        foreach ($majors as $major) {
            $chartLabels[] = $major->code ?? $major->name; // Gunakan kode jurusan
            $chartData[] = Student::where('major_id', $major->id)->count();
        }

        // 3. Data Terbaru (5 Pendaftar Terakhir) -> BAGIAN BARU
        $recentStudents = Student::with('major')->orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalStudents', 'totalMajors', 'totalJournals', 'totalTemplates',
            'chartLabels', 'chartData', 'recentStudents'
        ));
    }
}