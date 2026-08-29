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

        // 1. Validasi Input Dasar
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

        // Pastikan tanggal PKL sudah diisi
        if (!$journal->start_date || !$journal->end_date) {
            return back()->withErrors(['date' => 'Harap isi Tanggal Mulai dan Selesai PKL di menu Data PKL terlebih dahulu.'])->withInput();
        }

        // Validasi Tanggal (Maksimal hari ini, dan harus berada di dalam periode PKL)
        $request->validate([
            'date' => [
                'required', 'date', 'before_or_equal:today',
                'after_or_equal:' . $journal->start_date->format('Y-m-d'),
                'before_or_equal:' . $journal->end_date->format('Y-m-d')
            ],
            // ... (Biarkan validasi lain di bawahnya tetap utuh, misal status/entry_time/activity/dll)
        ], [
            'date.after_or_equal' => 'Tanggal tidak valid. Kegiatan/Absensi tidak boleh sebelum periode PKL dimulai (' . $journal->start_date->format('d M Y') . ').',
            'date.before_or_equal' => 'Tanggal tidak valid. Maksimal adalah hari ini dan tidak melebihi akhir PKL.',
        ]);

        // 3. Simpan Data Jika Validasi Lolos
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

    // Instruktur melakukan persetujuan melalui perangkat/akun siswa
    public function approve(Request $request, $journalId, $activityId)
    {
        $journal = Journal::where('id', $journalId)
                          ->where('student_id', Auth::user()->student->id)
                          ->firstOrFail();

        $activity = DailyActivity::where('id', $activityId)
                                 ->where('journal_id', $journal->id)
                                 ->firstOrFail();

        $request->validate([
            'instructor_notes' => 'nullable|string|max:255'
        ]);

        $activity->update([
            'is_approved' => true,
            'instructor_notes' => $request->instructor_notes
        ]);

        return back()->with('success', 'Kegiatan berhasil divalidasi oleh Instruktur.');
    }
}