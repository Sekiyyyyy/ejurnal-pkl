<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assessment;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. MONITORING (10 Item)
        $monitorings = [
            'Peserta didik dan instruktur menyepakati program PKL.',
            'Materi PKL yang diikuti peserta didik sesuai dengan hasil pemetaan kompetensi.',
            'Peserta didik mengisi Jurnal Kegiatan PKL secara lengkap.',
            'Peserta didik mendokumentasikan proses/prosedur portofolio.',
            'Pembelajaran PKL menambah wawasan dan pengalaman nyata.',
            'Pembelajaran PKL menambah pengetahuan sesuai Kompetensi Keahlian.',
            'Pembelajaran PKL menambah keterampilan sesuai Kompetensi Keahlian.',
            'Pembelajaran PKL menanamkan nilai-nilai karakter budaya industri.',
            'Pembimbingan selama pembelajaran di DUDIKA berjalan dengan baik.',
            'Selama pembelajaran di DUDIKA peserta didik tidak mengalami hambatan berarti.'
        ];
        
        foreach ($monitorings as $index => $item) {
            Assessment::create(['category' => 'monitoring', 'name' => $item, 'order_number' => $index + 1]);
        }

        // 2. OBSERVASI (Poin Utama + Sub Poin)
        $obs1 = Assessment::create(['category' => 'observation_point', 'name' => 'Menerapkan soft skills dan budaya kerja yang dibutuhkan dalam dunia kerja', 'order_number' => 1]);
        Assessment::create(['category' => 'observation_sub', 'parent_id' => $obs1->id, 'name' => 'Menunjukkan integritas dan kedisiplinan', 'order_number' => 1]);
        Assessment::create(['category' => 'observation_sub', 'parent_id' => $obs1->id, 'name' => 'Menunjukkan kemandirian, kreatifitas, dan rasa ingin tahu', 'order_number' => 2]);
        Assessment::create(['category' => 'observation_sub', 'parent_id' => $obs1->id, 'name' => 'Melaksanakan tanggung jawab dan kerja keras', 'order_number' => 3]);
        Assessment::create(['category' => 'observation_sub', 'parent_id' => $obs1->id, 'name' => 'Menunjukkan kepedulian sosial dan lingkungan', 'order_number' => 4]);
        Assessment::create(['category' => 'observation_sub', 'parent_id' => $obs1->id, 'name' => 'Menunjukkan komunikasi dan kolaboratif', 'order_number' => 5]);

        $obs2 = Assessment::create(['category' => 'observation_point', 'name' => 'Menerapkan SOP dan K3LH yang ada di dunia kerja', 'order_number' => 2]);
        Assessment::create(['category' => 'observation_sub', 'parent_id' => $obs2->id, 'name' => 'Menggunakan APD dengan tertib dan benar', 'order_number' => 1]);
        Assessment::create(['category' => 'observation_sub', 'parent_id' => $obs2->id, 'name' => 'Melaksanakan pekerjaan sesuai dengan SOP', 'order_number' => 2]);
        Assessment::create(['category' => 'observation_sub', 'parent_id' => $obs2->id, 'name' => 'Menerapkan K3LH', 'order_number' => 3]);

        $obs3 = Assessment::create(['category' => 'observation_point', 'name' => 'Menerapkan kompetensi teknis yang sudah dipelajari', 'order_number' => 3]);
        // 5 Slot kosong untuk diisi teks manual nantinya
        for ($i=1; $i<=5; $i++) {
            Assessment::create(['category' => 'observation_sub', 'parent_id' => $obs3->id, 'name' => 'Slot Teknis '.$i, 'order_number' => $i]);
        }

        // TAMBAHAN: POIN 4 OBSERVASI
        $obs4 = Assessment::create(['category' => 'observation_point', 'name' => 'Memahami alur bisnis dunia kerja tempat PKL dan wawasan wirausaha', 'order_number' => 4]);
        Assessment::create(['category' => 'observation_sub', 'parent_id' => $obs4->id, 'name' => 'Mengidentifikasi kegiatan bisnis atau usaha di dunia kerja', 'order_number' => 1]);
        Assessment::create(['category' => 'observation_sub', 'parent_id' => $obs4->id, 'name' => 'Menjelaskan alur bisnis atau usaha yang akan dilaksakan', 'order_number' => 2]);

        // 3. PENILAIAN TEKNIS (4 Poin)
        Assessment::create(['category' => 'grade_technical', 'name' => 'Menerapkan soft skills dan budaya kerja', 'order_number' => 1]);
        Assessment::create(['category' => 'grade_technical', 'name' => 'Menerapkan SOP dan K3LH', 'order_number' => 2]);
        Assessment::create(['category' => 'grade_technical', 'name' => 'Menerapkan kompetensi teknis', 'order_number' => 3]);
        Assessment::create(['category' => 'grade_technical', 'name' => 'Memahami alur bisnis dunia kerja', 'order_number' => 4]);

        // 4. PENILAIAN TEKNIS CUSTOM (5 Slot)
        for ($i=1; $i<=5; $i++) {
            Assessment::create(['category' => 'grade_custom', 'name' => 'Kompetensi Aktivitas '.$i, 'order_number' => $i]);
        }

        // 5. PENILAIAN BUDAYA KERJA (Non Teknis - 5 Poin)
        $nonTechs = ['Disiplin', 'Mandiri, Kreatif, dan Rasa Ingin Tahu', 'Tanggung Jawab dan Kerja Keras', 'Peduli Lingkungan dan Sosial', 'Komunikatif dan Berkolaborasi'];
        foreach ($nonTechs as $index => $item) {
            Assessment::create(['category' => 'grade_non_technical', 'name' => $item, 'order_number' => $index + 1]);
        }
    }
}