<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\DailyActivity;
use Illuminate\Support\Facades\Auth;

class DailyActivityController extends Controller
{
    public function index($journalId)
    {
        $journal = Journal::where('id', $journalId)
                          ->where('student_id', Auth::user()->student->id)
                          ->firstOrFail();

        // Ambil data kegiatan, urutkan dari tanggal terbaru lalu jam mulai terbaru
        $activities = $journal->dailyActivities()
                              ->orderBy('date', 'desc')
                              ->orderBy('start_time', 'desc')
                              ->get();

        return view('student.journal.activity', compact('journal', 'activities'));
    }

    public function store(Request $request, $journalId)
    {
        $journal = Journal::where('id', $journalId)
                          ->where('student_id', Auth::user()->student->id)
                          ->firstOrFail();

        // Validasi, pastikan kegiatan tidak diisi untuk hari esok
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'division' => 'nullable|string|max:255',
            'activity' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'character_values' => 'nullable|string|max:255',
        ], [
            'date.before_or_equal' => 'Tanggal kegiatan tidak boleh mendahului waktu saat ini.',
            'end_time.after' => 'Jam selesai harus lebih besar dari jam mulai.'
        ]);

        $journal->dailyActivities()->create([
            'date' => $request->date,
            'division' => $request->division,
            'activity' => $request->activity,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'character_values' => $request->character_values,
        ]);

        return back()->with('success', 'Kegiatan harian berhasil ditambahkan.');
    }
}