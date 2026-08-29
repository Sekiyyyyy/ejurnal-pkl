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
                          ->with(['student.major', 'company', 'teacher', 'dailyActivities', 'attendances', 'assessments.assessment'])
                          ->firstOrFail();

        // 1. Inisialisasi Template Word dari Folder Storage (Spesifik TKJ)
        $templatePath = storage_path('app/templates/template_jurnal_tkj.docx');
        
        if (!file_exists($templatePath)) {
            return back()->withErrors(['access' => 'File template_jurnal_tkj.docx tidak ditemukan di direktori storage/app/templates/.']);
        }
        $templateProcessor = new TemplateProcessor($templatePath);

        // 2. MAPPING DATA IDENTITAS & PKL
        $templateProcessor->setValue('siswa_nama', $journal->student->name ?? '-');
        $templateProcessor->setValue('siswa_nisn', $journal->student->nisn ?? '-');
        $templateProcessor->setValue('siswa_kelas', $journal->student->class ?? '-');
        // Catatan: Parameter di bawah ini diberi string kosong/default jika belum ada di tabel student
        $templateProcessor->setValue('siswa_ttl', '-'); 
        $templateProcessor->setValue('siswa_jk', '-');
        $templateProcessor->setValue('siswa_agama', '-');
        $templateProcessor->setValue('siswa_alamat', '-');
        $templateProcessor->setValue('siswa_hp', '-');
        $templateProcessor->setValue('siswa_email', $journal->student->user->email ?? '-');
        
        $templateProcessor->setValue('ortu_nama', '-');
        $templateProcessor->setValue('ortu_alamat', '-');
        $templateProcessor->setValue('ortu_hp', '-');

        $templateProcessor->setValue('guru_nama', $journal->teacher_name ?? '-');
        $templateProcessor->setValue('guru_alamat', '-'); // Kosongkan atau hapus dari template
        $templateProcessor->setValue('guru_hp', '-');

        $templateProcessor->setValue('pkl_nama', $journal->company_name ?? '-');
        $templateProcessor->setValue('pkl_alamat', $journal->company_address ?? '-');
        $templateProcessor->setValue('pkl_periode', ($journal->start_date ? $journal->start_date->format('d M Y') : '-') . ' s/d ' . ($journal->end_date ? $journal->end_date->format('d M Y') : '-'));

        $templateProcessor->setValue('instruktur_nama', $journal->instructor_name ?? '-');
        $templateProcessor->setValue('instruktur_jabatan', $journal->instructor_position ?? '-');
        $templateProcessor->setValue('instruktur_alamat', '-');
        $templateProcessor->setValue('instruktur_hp', $journal->instructor_phone ?? '-');

        // 3. MAPPING KEHADIRAN
        $hadirSakit = $journal->attendances->where('status', 'Sakit')->count();
        $hadirIzin = $journal->attendances->where('status', 'Izin')->count();
        $hadirAlpa = $journal->attendances->where('status', 'Alpa')->count();
        $templateProcessor->setValue('hadir_sakit', $hadirSakit);
        $templateProcessor->setValue('hadir_izin', $hadirIzin);
        $templateProcessor->setValue('hadir_alpa', $hadirAlpa);

        // 4. MAPPING LOGBOOK KEGIATAN HARIAN (Clone Row berdasarkan ${keg_tanggal})
        $activities = $journal->dailyActivities;
        if ($activities->count() > 0) {
            $templateProcessor->cloneRow('keg_tanggal', $activities->count());
            foreach ($activities as $index => $act) {
                $row = $index + 1;
                $templateProcessor->setValue("keg_tanggal#{$row}", $act->date->format('d/m/Y'));
                $templateProcessor->setValue("keg_aktivitas#{$row}", $act->activity);
                $templateProcessor->setValue("keg_divisi#{$row}", $act->division ?? '-');
                $templateProcessor->setValue("keg_mulai#{$row}", \Carbon\Carbon::parse($act->start_time)->format('H:i'));
                $templateProcessor->setValue("keg_selesai#{$row}", \Carbon\Carbon::parse($act->end_time)->format('H:i'));
                $templateProcessor->setValue("keg_karakter#{$row}", $act->character_values ?? '-');
                $templateProcessor->setValue("keg_catatan#{$row}", $act->instructor_notes ?? '-');
                
                // Tempel paraf jika sudah di-ACC
                if ($act->is_approved && $journal->instructor_paraf) {
                    $templateProcessor->setImageValue("paraf_instruktur#{$row}", [
                        'path' => storage_path('app/public/' . $journal->instructor_paraf),
                        'width' => 50, 'height' => 30, 'ratio' => false
                    ]);
                } else {
                    $templateProcessor->setValue("paraf_instruktur#{$row}", "");
                }
            }
        } else {
            $templateProcessor->cloneRow('keg_tanggal', 0);
        }

        // --- FUNGSI HELPER KUALIFIKASI ---
        $getKualifikasi = function($score) {
            if ($score === null || $score === '') return '-';
            if ($score >= 90) return 'Sangat Kompeten';
            if ($score >= 80) return 'Kompeten';
            if ($score >= 70) return 'Cukup Kompeten';
            return 'Belum Kompeten';
        };

        // 5. PRE-FILL SEMUA PARAMETER ASESMEN DENGAN KOSONG (Mencegah error jika belum dinilai)
        // Monitoring
        for ($i=1; $i<=10; $i++) {
            $templateProcessor->setValue("m{$i}_y", "");
            $templateProcessor->setValue("m{$i}_t", "");
        }
        // Observasi
        for ($i=1; $i<=4; $i++) {
            $templateProcessor->setValue("obs_d_{$i}", "");
            $templateProcessor->setValue("obs_c_{$i}_0", "");
            for ($j=1; $j<=5; $j++) {
                $templateProcessor->setValue("obs_c_{$i}_{$j}", "");
                if ($i == 3) $templateProcessor->setValue("obs_tek_{$j}", "");
            }
        }
        // Penilaian Teknis
        for ($i=1; $i<=4; $i++) {
            $templateProcessor->setValue("nilai_tp_{$i}", "");
            $templateProcessor->setValue("kual_tp_{$i}", "");
        }
        // Penilaian Custom
        for ($i=1; $i<=5; $i++) {
            $templateProcessor->setValue("pt_nama_{$i}", "");
            $templateProcessor->setValue("pt_nilai_{$i}", "");
            $templateProcessor->setValue("pt_kual_{$i}", "");
        }
        // Penilaian Non-Teknis
        for ($i=1; $i<=5; $i++) {
            $templateProcessor->setValue("nilai_nt_{$i}", "");
            $templateProcessor->setValue("kual_nt_{$i}", "");
        }
        // Catatan Observasi Tambahan
        $templateProcessor->setValue("catatan_obs_guru", "");
        $templateProcessor->setValue("catatan_obs_instruktur", "");

        // 6. MAPPING DATA ASESMEN DARI DATABASE
        $assessments = $journal->assessments;
        foreach ($assessments as $ja) {
            $cat = $ja->assessment->category;
            $order = $ja->assessment->order_number;
            $parentOrder = $ja->assessment->parent_id ? Assessment::find($ja->assessment->parent_id)->order_number : null;

            // Lembar Monitoring (Centang V)
            if ($cat == 'monitoring') {
                $templateProcessor->setValue("m{$order}_y", $ja->is_yes === 1 ? 'V' : '');
                $templateProcessor->setValue("m{$order}_t", $ja->is_yes === 0 ? 'V' : '');
            }
            
            // Lembar Observasi Ketercapaian
            if ($cat == 'observation_point') {
                $templateProcessor->setValue("obs_d_{$order}", $ja->description ?? '');
            }
            if ($cat == 'observation_sub' && $parentOrder) {
                // Untuk poin 3, ambil nama kompetensinya
                if ($parentOrder == 3) {
                    $templateProcessor->setValue("obs_tek_{$order}", $ja->description ?? '');
                }
                // Mapping Ketercapaian (Ya/Tidak)
                $ketercapaian = '';
                if ($ja->is_yes === 1) $ketercapaian = 'Ya';
                if ($ja->is_yes === 0) $ketercapaian = 'Tidak';
                $templateProcessor->setValue("obs_c_{$parentOrder}_{$order}", $ketercapaian);
            }

            // Penilaian Angka & Kualifikasi
            if ($cat == 'grade_technical') {
                $templateProcessor->setValue("nilai_tp_{$order}", $ja->score ?? '');
                $templateProcessor->setValue("kual_tp_{$order}", $getKualifikasi($ja->score));
            }
            if ($cat == 'grade_custom') {
                $templateProcessor->setValue("pt_nama_{$order}", $ja->description ?? '');
                $templateProcessor->setValue("pt_nilai_{$order}", $ja->score ?? '');
                $templateProcessor->setValue("pt_kual_{$order}", $getKualifikasi($ja->score));
            }
            if ($cat == 'grade_non_technical') {
                $templateProcessor->setValue("nilai_nt_{$order}", $ja->score ?? '');
                $templateProcessor->setValue("kual_nt_{$order}", $getKualifikasi($ja->score));
            }
        }

        // 7. MAPPING GAMBAR TANDA TANGAN
        $signatures = [
            'ttd_siswa' => $journal->student_signature,
            'ttd_instruktur' => $journal->instructor_signature,
            'ttd_ortu' => $journal->parent_signature,
            'ttd_guru' => $journal->teacher->signature ?? null,
            'ttd_kaprog' => null, // Sesuai konfigurasi admin nanti
        ];

        foreach ($signatures as $placeholder => $path) {
            if ($path && file_exists(storage_path('app/public/' . $path))) {
                $templateProcessor->setImageValue($placeholder, [
                    'path' => storage_path('app/public/' . $path),
                    'width' => 100, 'height' => 80, 'ratio' => true
                ]);
            } else {
                $templateProcessor->setValue($placeholder, ""); // Kosongkan jika belum ada gambar
            }
        }

        // 8. KUNCI JURNAL & DOWNLOAD
        if ($journal->status !== 'COMPLETED') {
            $journal->update(['status' => 'COMPLETED']);
        }

        $fileName = 'Jurnal_PKL_' . str_replace(' ', '_', $journal->student->name) . '_Fase_' . $journal->phase . '.docx';
        $tempPath = storage_path('app/public/' . $fileName);
        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}