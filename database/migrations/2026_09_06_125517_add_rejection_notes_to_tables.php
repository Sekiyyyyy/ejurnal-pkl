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
        Schema::table('journals', function (Blueprint $table) {
            $table->text('instructor_rejection_note')->nullable()->after('instructor_live_photo');
            $table->text('teacher_rejection_note')->nullable()->after('teacher_live_photo');
        });

        Schema::table('weekly_approvals', function (Blueprint $table) {
            $table->text('rejection_note')->nullable()->after('approved_at');
            $table->boolean('is_rejected')->default(false)->after('rejection_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_approvals', function (Blueprint $table) {
            $table->dropColumn(['rejection_note', 'is_rejected']);
        });

        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn(['instructor_rejection_note', 'teacher_rejection_note']);
        });
    }
};
