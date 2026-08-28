<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            
            // Kolom dinamis (diisi sesuai 'category' dari master assessment)
            $table->boolean('is_yes')->nullable(); // Untuk Monitoring & Observasi Sub
            $table->integer('score')->nullable(); // Untuk Penilaian Angka (0-100)
            $table->text('description')->nullable(); // Untuk Deskripsi Observasi ATAU Nama Kompetensi Custom
            
            $table->timestamps();
            
            // Mencegah duplikasi penilaian untuk aspek yang sama pada 1 jurnal
            $table->unique(['journal_id', 'assessment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_assessments');
    }
};