<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            // user_id nullable. Terisi saat siswa berhasil register dengan NISN ini.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('major_id')->constrained('majors')->restrictOnDelete();
            
            $table->string('nisn')->unique();
            $table->string('name');
            $table->string('class')->nullable(); // Contoh: XII TKJ 1
            $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            
            // Data Orang Tua
            $table->string('parent_name')->nullable();
            $table->text('parent_address')->nullable();
            $table->string('parent_phone')->nullable();
            
            // TAMBAHAN BARU: Timestamp untuk pelacakan Jurnal dan Filter 3 Bulan
            $table->timestamp('jurnal_1_completed_at')->nullable();
            $table->timestamp('jurnal_2_completed_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};