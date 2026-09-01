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

        $activities = $journal->dailyActivities()
                              ->orderBy('date', 'desc')
                              ->get();

        return view('student.journal.activity', compact('journal', 'activities'));
    }

    public function store(Request $request, $journalId)
    {
        $journal = Journal::where('id', $journalId)
                          ->where('student_id', Auth::user()->student->id)
                          ->firstOrFail();

        if (!$journal->start_date || !$journal->end_date) {
            return back()->withErrors(['date' => 'Harap isi Tanggal Mulai dan Selesai PKL di menu Data PKL terlebih dahulu.'])->withInput();
        }

        // 1. Validasi Dasar
        $request->validate([
            'date' => [
                'required', 'date', 'before_or_equal:today',
                'after_or_equal:' . $journal->start_date->format('Y-m-d'),
                'before_or_equal:' . $journal->end_date->format('Y-m-d')
            ],
            'status' => 'required|in:Hadir,Sakit,Izin,Alpa,Libur',
            'division' => 'nullable|string|max:255',
            'activity' => 'nullable|string',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'character_values' => 'nullable|string|max:255',
        ]);

        // Jika status HADIR, jam, divisi, dan uraian pekerjaan wajib diisi
        if ($request->status === 'Hadir') {
            if (!$request->start_time || !$request->end_time || empty($request->activity) || empty($request->division)) {
                return back()->withErrors(['activity' => 'Untuk status Hadir, Jam Masuk/Pulang, Divisi, dan Uraian Pekerjaan wajib diisi.'])->withInput();
            }
        }

        // Cek apakah tanggal tersebut sudah pernah diisi sebelumnya
        $exists = DailyActivity::where('journal_id', $journal->id)
                               ->where('date', $request->date)
                               ->exists();

        if ($exists) {
            return back()->withErrors(['date' => 'Catatan untuk tanggal ' . $request->date . ' sudah ada. Gunakan fitur edit jika ingin mengubahnya.'])->withInput();
        }

        // Simpan Data
        $journal->dailyActivities()->create([
            'date' => $request->date,
            'status' => $request->status,
            'division' => $request->status === 'Hadir' ? $request->division : null,
            'activity' => $request->status === 'Hadir' ? $request->activity : 'Keterangan: ' . $request->status,
            'start_time' => $request->status === 'Hadir' ? $request->start_time : null,
            'end_time' => $request->status === 'Hadir' ? $request->end_time : null,
            'character_values' => $request->status === 'Hadir' ? $request->character_values : null,
        ]);

        return back()->with('success', 'Catatan harian berhasil ditambahkan.');
    }

    // Edit Form (Opsional jika menggunakan halaman terpisah, atau bisa via modal)
    public function edit($journalId, $activityId)
    {
        $journal = Journal::where('id', $journalId)->where('student_id', Auth::user()->student->id)->firstOrFail();
        $activity = DailyActivity::where('id', $activityId)->where('journal_id', $journal->id)->firstOrFail();

        // CEK APAKAH SUDAH DI-ACC / PARAF
        if ($activity->is_approved) {
            return redirect()->route('journal.activity', $journal->id)
                             ->withErrors(['error' => 'Aksi Ditolak! Kegiatan pada tanggal ini sudah di-ACC/paraf oleh instruktur dan tidak dapat diubah.']);
        }

        return view('student.journal.edit-activity', compact('journal', 'activity'));
    }

    public function update(Request $request, $journalId, $activityId)
    {
        $journal = Journal::where('id', $journalId)->where('student_id', Auth::user()->student->id)->firstOrFail();
        $activity = DailyActivity::where('id', $activityId)->where('journal_id', $journal->id)->firstOrFail();

        // CEK APAKAH SUDAH DI-ACC / PARAF
        if ($activity->is_approved) {
            return back()->withErrors(['error' => 'Kegiatan ini sudah disetujui instruktur, perubahan diblokir.']);
        }

        $request->validate([
            'status' => 'required|in:Hadir,Sakit,Izin,Alpa,Libur',
            'division' => 'nullable|string|max:255',
            'activity' => 'nullable|string',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);

        $activity->update([
            'status' => $request->status,
            'division' => $request->status === 'Hadir' ? $request->division : null,
            'activity' => $request->status === 'Hadir' ? $request->activity : 'Keterangan: ' . $request->status,
            'start_time' => $request->status === 'Hadir' ? $request->start_time : null,
            'end_time' => $request->status === 'Hadir' ? $request->end_time : null,
            'character_values' => $request->status === 'Hadir' ? $request->character_values : null,
        ]);

        return redirect()->route('journal.activity', $journal->id)->with('success', 'Kegiatan harian berhasil diperbarui.');
    }

    public function approve(Request $request, $journalId, $activityId)
    {
        $journal = Journal::where('id', $journalId)
                          ->where('student_id', Auth::user()->student->id)
                          ->firstOrFail();

        $activity = DailyActivity::where('id', $activityId)
                                   ->where('journal_id', $journal->id)
                                   ->firstOrFail();

        $request->validate([
            'instructor_notes' => 'required|string|max:255'
        ], [
            'instructor_notes.required' => 'Catatan instruktur wajib diisi sebelum melakukan ACC.'
        ]);

        $activity->update([
            'is_approved' => true,
            'instructor_notes' => $request->instructor_notes
        ]);

        return back()->with('success', 'Catatan harian berhasil divalidasi oleh Instruktur.');
    }

    public function destroy($journalId, $activityId)
    {
        $journal = Journal::where('id', $journalId)->where('student_id', Auth::user()->student->id)->firstOrFail();
        $activity = DailyActivity::where('id', $activityId)->where('journal_id', $journal->id)->firstOrFail();

        if ($activity->is_approved) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus kegiatan yang sudah di-ACC instruktur!']);
        }

        $activity->delete();

        return back()->with('success', 'Catatan berhasil dihapus.');
    }
}