<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            // Menyimpan path gambar tanda tangan (Maksimal 1MB)
            $table->string('student_signature')->nullable();
            $table->string('parent_signature')->nullable();
            $table->string('instructor_signature')->nullable();
            $table->string('instructor_paraf')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn(['student_signature', 'parent_signature', 'instructor_signature', 'instructor_paraf']);
        });
    }
};