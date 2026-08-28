<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->cascadeOnDelete();
            
            $table->date('date');
            $table->string('division')->nullable(); // Divisi/Departemen tempat siswa bertugas
            $table->text('activity'); // Uraian pekerjaan
            $table->time('start_time');
            $table->time('end_time');
            $table->string('character_values')->nullable(); // Mandiri, Disiplin, Teamwork, dll
            
            // Kolom ini tidak diisi oleh siswa, melainkan oleh Instruktur di tahap review nanti
            $table->text('instructor_notes')->nullable(); 
            $table->boolean('is_approved')->default(false); // Sebagai pengganti tanda tangan/paraf digital
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_activities');
    }
};