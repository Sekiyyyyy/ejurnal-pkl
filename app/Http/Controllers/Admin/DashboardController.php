<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Journal;
use App\Models\Major;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung statistik sederhana untuk ditampilkan
        $totalStudents = Student::count();
        $totalMajors = Major::count();
        $totalJournals = Journal::count();
        
        // Hitung jurnal yang sudah 100% selesai
        $completedJournals = Journal::where('status', 'COMPLETED')->count();

        return view('admin.dashboard', compact(
            'totalStudents', 'totalMajors', 'totalJournals', 'completedJournals'
        ));
    }
}