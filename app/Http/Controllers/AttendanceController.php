<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // Menampilkan halaman daftar & form absensi
    public function index($journalId)
    {
        $journal = Journal::where('id', $journalId)
                        ->where('student_id', Auth::user()->student->id)
                        ->firstOrFail();

        // Ambil data absensi, urutkan dari tanggal terbaru
        $attendances = $journal->attendances()->orderBy('date', 'desc')->get();

        return view('student.journal.attendance', compact('journal', 'attendances'));
    }

    public function store(Request $request, $journalId)
    {
        $journal = Journal::where('id', $journalId)
                          ->where('student_id', Auth::user()->student->id)
                          ->firstOrFail();

        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:Hadir,Sakit,Izin,Alpa,Libur',
            'entry_time' => 'nullable|date_format:H:i',
            'exit_time' => 'nullable|date_format:H:i|after:entry_time',
            'notes' => 'nullable|string|max:255',
        ], [
            'date.before_or_equal' => 'Tanggal absensi tidak boleh mendahului waktu saat ini.'
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

        Attendance::create([
            'journal_id' => $journal->id,
            'date' => $request->date,
            'status' => $request->status,
            'entry_time' => $request->status === 'Hadir' ? $request->entry_time : null,
            'exit_time' => $request->status === 'Hadir' ? $request->exit_time : null,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Kehadiran berhasil ditambahkan.');
    }
}