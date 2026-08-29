<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            // Relasi Utama
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();

            $table->tinyInteger('phase')->comment('1 untuk PKL 1, 2 untuk PKL 2');
            
            $table->string('company_name')->nullable();
            $table->string('company_address')->nullable();
            $table->string('teacher_name')->nullable();
            
            // Data Instruktur (Penanggung Jawab PKL)
            $table->string('instructor_name')->nullable();
            $table->string('instructor_position')->nullable();
            $table->string('instructor_email')->nullable();
            $table->string('instructor_phone')->nullable();
            
            // Periode PKL
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            // Status Jurnal
            $table->enum('status', [
                'DRAFT', 
                'IN_PROGRESS', 
                'WAITING_REVIEW', 
                'REVIEWED', 
                'COMPLETED', 
                'READY_TO_GENERATE', 
                'GENERATED'
            ])->default('DRAFT');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};