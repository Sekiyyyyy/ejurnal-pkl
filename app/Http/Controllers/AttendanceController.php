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

    // Menyimpan data absensi baru
    public function store(Request $request, $journalId)
    {
        $journal = Journal::where('id', $journalId)
                        ->where('student_id', Auth::user()->student->id)
                        ->firstOrFail();

        $request->validate([
            // Validasi agar tanggal tidak melebihi hari ini
            'date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:Hadir,Sakit,Izin,Alpa,Libur',
            'entry_time' => 'nullable|date_format:H:i',
            'exit_time' => 'nullable|date_format:H:i|after:entry_time',
            'notes' => 'nullable|string|max:255',
            // Validasi file bukti (opsional, maks 2MB, format gambar/pdf)
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'date.before_or_equal' => 'Tanggal absensi tidak boleh mendahului waktu (maksimal hari ini).'
        ]);

        // Cek apakah tanggal sudah pernah diisi
        $exists = Attendance::where('journal_id', $journal->id)->where('date', $request->date)->exists();
        if ($exists) {
            return back()->withErrors(['date' => 'Absensi untuk tanggal ini sudah diisi.'])->withInput();
        }

        // Proses Upload File jika ada
        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('attendances', 'public');
        }

        Attendance::create([
            'journal_id' => $journal->id,
            'date' => $request->date,
            'status' => $request->status,
            'entry_time' => $request->status === 'Hadir' ? $request->entry_time : null,
            'exit_time' => $request->status === 'Hadir' ? $request->exit_time : null,
            'notes' => $request->notes,
            'evidence_path' => $evidencePath,
        ]);

        return back()->with('success', 'Kehadiran berhasil ditambahkan.');
    }
}