<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Major;
use App\Models\Assessment;

class PplgAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Jurusan PPLG (Jika belum ada)
        $pplg = Major::firstOrCreate(
            ['code' => 'PPLG'],
            ['name' => 'Pengembangan Perangkat Lunak dan Gim']
        );

        // Mencegah duplikasi jika Seeder dijalankan 2 kali
        if (Assessment::where('major_id', $pplg->id)->exists()) {
            $this->command->info('Data penilaian PPLG sudah ada, proses dihentikan agar tidak dobel.');
            return;
        }

        $tkjId = 1; // ID Jurusan TKJ yang akan dijadikan sumber copy-paste
        $parentsMap = []; // Keranjang untuk mengingat ID Induk yang baru

        // 2. COPY DATA UTAMA (Selain Sub-Poin)
        $mainAssessments = Assessment::where('major_id', $tkjId)
                                     ->where('category', '!=', 'observation_sub')
                                     ->get();

        foreach ($mainAssessments as $old) {
            $new = $old->replicate(); // Fitur ajaib Laravel untuk duplikat data
            $new->major_id = $pplg->id; // Ganti kepemilikannya ke PPLG
            $new->save();

            // Jika yang diduplikat adalah "Judul Utama Observasi", simpan ID barunya
            if ($old->category === 'observation_point') {
                $parentsMap[$old->id] = $new->id;
            }
        }

        // 3. COPY DATA SUB-POIN (Lalu pasangkan dengan Induk yang baru)
        $subAssessments = Assessment::where('major_id', $tkjId)
                                    ->where('category', 'observation_sub')
                                    ->get();

        foreach ($subAssessments as $oldSub) {
            $newSub = $oldSub->replicate();
            $newSub->major_id = $pplg->id;
            
            // Cocokkan parent_id dengan ID Induk PPLG (bukan Induk TKJ)
            $newSub->parent_id = $parentsMap[$oldSub->parent_id] ?? null;
            $newSub->save();
        }

        $this->command->info('Sukses! Jurusan PPLG berhasil ditambahkan beserta 43 duplikat kriteria penilaiannya.');
    }
}