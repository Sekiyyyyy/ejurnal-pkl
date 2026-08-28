<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            // Jika major_id NULL, berarti aspek ini berlaku untuk SEMUA jurusan. 
            // Jika diisi, hanya berlaku untuk jurusan tersebut.
            $table->foreignId('major_id')->nullable()->constrained('majors')->cascadeOnDelete();
            
            // Kategori Penilaian
            $table->enum('category', [
                'monitoring',         // Lembar Monitoring (Ya/Tidak)
                'observation_point',  // Poin Utama Observasi (Butuh Deskripsi)
                'observation_sub',    // Sub-poin Observasi (Ya/Tidak)
                'grade_technical',    // Penilaian Teknis Utama (Angka 0-100)
                'grade_custom',       // Penilaian Teknis Dinamis (Angka + Input Nama Kompetensi)
                'grade_non_technical' // Penilaian Budaya Kerja (Angka 0-100)
            ]);
            
            // Untuk Sub-poin observasi yang menginduk ke poin utama
            $table->foreignId('parent_id')->nullable()->constrained('assessments')->cascadeOnDelete();
            
            $table->string('name'); // Nama Aspek (Contoh: "Disiplin", "Menerapkan K3LH")
            $table->integer('order_number')->default(0); // Urutan tampil
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};