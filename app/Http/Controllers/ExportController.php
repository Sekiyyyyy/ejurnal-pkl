<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\Assessment;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;

class ExportController extends Controller
{
    public function exportDocx($id)
    {
        $journal = Journal::where('id', $id)
                            ->where('student_id', Auth::user()->student->id)
                            ->with(['student.major', 'dailyActivities', 'attendances', 'assessments.assessment'])
                            ->firstOrFail();

        // 1. CARI TEMPLATE YANG AKTIF DI DATABASE
        $activeTemplate = \App\Models\Template::where('is_active', true)->first();
        
        if (!$activeTemplate) {
            return back()->withErrors(['error' => 'Sistem gagal mencetak: Tidak ada Template Word yang aktif. Harap upload dan aktifkan di panel Admin.']);
        }

        // Tentukan path ke template yang diupload (di dalam folder public)
        $templatePath = storage_path('app/public/' . $activeTemplate->file_path);
        
        if (!file_exists($templatePath)) {
            return back()->withErrors(['error' => 'File template fisik tidak ditemukan di server.']);
        }

        // 2. INISIALISASI TEMPLATE PROCESSOR
        $templateProcessor = new TemplateProcessor($templatePath);

        // Fungsi aman untuk mengisi string (mencegah null / error XML)
        $setVal = function($key, $val) use ($templateProcessor) {
            $templateProcessor->setValue($key, $val !== null ? htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8') : '');
        };

        // 1. IDENTITAS & DATA PKL
        $setVal('siswa_nama', $journal->student->name ?? '-');
        $setVal('siswa_nisn', $journal->student->nisn ?? '-');
        $setVal('siswa_kelas', $journal->student->class ?? '-');
        
        // Format: Medan, 17 Agustus 2005
        $ttl = '-';
        if ($journal->student->birth_place && $journal->student->birth_date) {
            $ttl = $journal->student->birth_place . ', ' . \Carbon\Carbon::parse($journal->student->birth_date)->format('d M Y');
        }
        $setVal('siswa_ttl', $ttl);
        
        $setVal('siswa_jk', $journal->student->gender ?? '-');
        $setVal('siswa_agama', $journal->student->religion ?? '-');
        $setVal('siswa_alamat', $journal->student->address ?? '-');
        $setVal('siswa_hp', $journal->student->phone ?? '-');
        $setVal('siswa_email', $journal->student->user->email ?? '-');
        
        $setVal('ortu_nama', $journal->student->parent_name ?? '-');
        $setVal('ortu_alamat', $journal->student->parent_address ?? '-');
        $setVal('ortu_hp', $journal->student->parent_phone ?? '-');

        // Data Guru Pembimbing
        $setVal('guru_nama', $journal->teacher_name ?? '-');
        $setVal('guru_alamat', $journal->teacher_address ?? '-');
        $setVal('guru_hp', $journal->teacher_phone ?? '-');

        // Data Instruktur DU/DI
        $setVal('instruktur_nama', $journal->instructor_name ?? '-');
        $setVal('instruktur_jabatan', $journal->instructor_position ?? '-');
        $setVal('instruktur_alamat', $journal->instructor_address ?? '-');
        $setVal('instruktur_hp', $journal->instructor_phone ?? '-');

        $setVal('pkl_nama', $journal->company_name ?? '-');
        $setVal('pkl_alamat', $journal->company_address ?? '-');
        $setVal('pkl_periode', ($journal->start_date ? $journal->start_date->format('d M Y') : '-') . ' s/d ' . ($journal->end_date ? $journal->end_date->format('d M Y') : '-'));

        // 2. KEHADIRAN
        $setVal('hadir_sakit', $journal->attendances->where('status', 'Sakit')->count());
        $setVal('hadir_izin', $journal->attendances->where('status', 'Izin')->count());
        $setVal('hadir_alpa', $journal->attendances->where('status', 'Alpa')->count());

        // 3. LOGBOOK KEGIATAN HARIAN
        $activities = $journal->dailyActivities;
        if ($activities->count() > 0) {
            $templateProcessor->cloneRow('keg_tanggal', $activities->count());
            foreach ($activities as $index => $act) {
                $row = $index + 1;
                $setVal("keg_tanggal#{$row}", $act->date->format('d/m/Y'));
                $setVal("keg_aktivitas#{$row}", $act->activity);
                $setVal("keg_divisi#{$row}", $act->division ?? '-');
                $setVal("keg_mulai#{$row}", \Carbon\Carbon::parse($act->start_time)->format('H:i'));
                $setVal("keg_selesai#{$row}", \Carbon\Carbon::parse($act->end_time)->format('H:i'));
                $setVal("keg_karakter#{$row}", $act->character_values ?? '-');
                $setVal("keg_catatan#{$row}", $act->instructor_notes ?? '-');
                
                $parafPath = $journal->instructor_paraf ? storage_path('app/public/' . $journal->instructor_paraf) : null;
                if ($act->is_approved && $parafPath && file_exists($parafPath)) {
                    $templateProcessor->setImageValue("paraf_instruktur#{$row}", [
                        'path' => $parafPath, 'width' => 40, 'height' => 25, 'ratio' => false
                    ]);
                } else {
                    $setVal("paraf_instruktur#{$row}", "");
                }
            }
        } else {
            $templateProcessor->cloneRow('keg_tanggal', 0);
        }

        $getKualifikasi = function($score) {
            if ($score === null || $score === '') return '-';
            if ($score >= 90) return 'Sangat Kompeten';
            if ($score >= 80) return 'Kompeten';
            if ($score >= 70) return 'Cukup Kompeten';
            return 'Belum Kompeten';
        };

        // 4. SIAPKAN BUFFER ARRAY UNTUK ASESMEN (Agar tag tidak hilang duluan)
        $assessmentData = [];
        for ($i=1; $i<=10; $i++) { $assessmentData["m{$i}_y"] = ""; $assessmentData["m{$i}_t"] = ""; }
        for ($i=1; $i<=4; $i++) {
            $assessmentData["obs_d_{$i}"] = ""; $assessmentData["obs_c_{$i}_0"] = "";
            for ($j=1; $j<=5; $j++) {
                $assessmentData["obs_c_{$i}_{$j}"] = "";
                if ($i == 3) $assessmentData["obs_tek_{$j}"] = "";
            }
        }
        for ($i=1; $i<=4; $i++) { $assessmentData["nilai_tp_{$i}"] = ""; $assessmentData["kual_tp_{$i}"] = ""; }
        for ($i=1; $i<=5; $i++) { $assessmentData["pt_nama_{$i}"] = ""; $assessmentData["pt_nilai_{$i}"] = ""; $assessmentData["pt_kual_{$i}"] = ""; }
        for ($i=1; $i<=5; $i++) { $assessmentData["nilai_nt_{$i}"] = ""; $assessmentData["kual_nt_{$i}"] = ""; }
        $assessmentData["catatan_obs_guru"] = "";
        $assessmentData["catatan_obs_instruktur"] = "";

        // Variabel untuk menampung Kalkulasi Nilai
        $sum_tp = 0; $count_tp = 0;
        $sum_pt = 0; $count_pt = 0;
        $sum_nt = 0; $count_nt = 0;

        // 5. MAPPING ASESMEN DARI DATABASE
        foreach ($journal->assessments as $ja) {
            $cat = $ja->assessment->category;
            $order = $ja->assessment->order_number;
            $parentOrder = $ja->assessment->parent_id ? Assessment::find($ja->assessment->parent_id)->order_number : null;

            if ($cat == 'monitoring') {
                // Menggunakan titik tengah (●) sebagai ganti V
                $assessmentData["m{$order}_y"] = $ja->is_yes == 1 ? '●' : '';
                $assessmentData["m{$order}_t"] = $ja->is_yes == 0 && $ja->is_yes !== null ? '●' : '';
            }
            if ($cat == 'observation_point') {
                $assessmentData["obs_d_{$order}"] = $ja->description ?? '';
                // UBAH BARIS INI: Dari "Tercapai" menjadi "Ya"
                $assessmentData["obs_c_{$order}_0"] = "Ya"; 
            }
            if ($cat == 'observation_sub' && $parentOrder) {
                if ($parentOrder == 3) {
                    $assessmentData["obs_tek_{$order}"] = $ja->description ?? '';
                }
                $ketercapaian = '';
                if ($ja->is_yes == 1) $ketercapaian = 'Ya';
                if ($ja->is_yes == 0 && $ja->is_yes !== null) $ketercapaian = 'Tidak';
                $assessmentData["obs_c_{$parentOrder}_{$order}"] = $ketercapaian;
            }
            if ($cat == 'grade_technical') {
                $assessmentData["nilai_tp_{$order}"] = $ja->score ?? '';
                $assessmentData["kual_tp_{$order}"] = $getKualifikasi($ja->score);
                if (is_numeric($ja->score)) { $sum_tp += $ja->score; $count_tp++; }
            }
            if ($cat == 'grade_custom') {
                $assessmentData["pt_nama_{$order}"] = $ja->description ?? '';
                $assessmentData["pt_nilai_{$order}"] = $ja->score ?? '';
                $assessmentData["pt_kual_{$order}"] = $getKualifikasi($ja->score);
                if (is_numeric($ja->score)) { $sum_pt += $ja->score; $count_pt++; }
            }
            if ($cat == 'grade_non_technical') {
                $assessmentData["nilai_nt_{$order}"] = $ja->score ?? '';
                $assessmentData["kual_nt_{$order}"] = $getKualifikasi($ja->score);
                if (is_numeric($ja->score)) { $sum_nt += $ja->score; $count_nt++; }
            }
        }

        // EKSEKUSI SET-VALUE UNTUK TAG ASESMEN
        foreach ($assessmentData as $key => $val) {
            $setVal($key, $val);
        }

        // EKSEKUSI SET-VALUE UNTUK JUMLAH & RATA-RATA (Dihitung Otomatis)
        $setVal('jml_tp', $sum_tp > 0 ? $sum_tp : '');
        $setVal('rata_tp', $count_tp > 0 ? round($sum_tp / $count_tp, 2) : '');

        $setVal('jml_pt', $sum_pt > 0 ? $sum_pt : '');
        $setVal('rata_pt', $count_pt > 0 ? round($sum_pt / $count_pt, 2) : '');

        $setVal('jml_nt', $sum_nt > 0 ? $sum_nt : '');
        $setVal('rata_nt', $count_nt > 0 ? round($sum_nt / $count_nt, 2) : '');

        // 6. MAPPING TANDA TANGAN (TERMASUK GURU & KAPROG)
        $signatures = [
            'ttd_siswa' => $journal->student_signature,
            'ttd_instruktur' => $journal->instructor_signature,
            'ttd_ortu' => $journal->parent_signature,
            'ttd_guru' => $journal->teacher_signature, // Data Baru
            'ttd_kaprog' => $journal->kaprog_signature, // Data Baru
        ];

        foreach ($signatures as $placeholder => $path) {
            $fullPath = $path ? storage_path('app/public/' . $path) : null;
            if ($fullPath && file_exists($fullPath)) {
                try {
                    $templateProcessor->setImageValue($placeholder, [
                        'path' => $fullPath, 'width' => 80, 'height' => 50, 'ratio' => true
                    ]);
                } catch (\Exception $e) {
                    $setVal($placeholder, "");
                }
            } else {
                $setVal($placeholder, ""); 
            }
        }

        if ($journal->status !== 'COMPLETED') {
            $journal->update(['status' => 'COMPLETED']);
        }

        $fileName = 'Jurnal_PKL_' . str_replace(' ', '_', $journal->student->name) . '_Fase_' . $journal->phase . '.docx';
        $tempPath = storage_path('app/public/' . $fileName);
        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}