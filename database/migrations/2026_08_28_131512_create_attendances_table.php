<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->cascadeOnDelete();
            
            $table->date('date');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpa', 'Libur'])->default('Hadir');
            $table->time('entry_time')->nullable();
            $table->time('exit_time')->nullable();
            $table->string('notes')->nullable();
            
            $table->timestamps();

            // Mencegah siswa absen 2 kali di tanggal yang sama untuk jurnal yang sama
            $table->unique(['journal_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};