<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->text('teacher_address')->nullable();
            $table->string('teacher_phone')->nullable();
            $table->text('instructor_address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn(['teacher_address', 'teacher_phone', 'instructor_address']);
        });
    }
};