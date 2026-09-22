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
        // 1. Indeks performa pada tabel journals
        Schema::table('journals', function (Blueprint $table) {
            $table->index(['student_id', 'phase'], 'idx_journals_student_phase');
            if (Schema::hasColumn('journals', 'kaprodi_status')) {
                $table->index('kaprodi_status', 'idx_journals_kaprodi_status');
            }
            $table->index('status', 'idx_journals_status');
        });

        // 2. Indeks performa pada tabel daily_activities
        Schema::table('daily_activities', function (Blueprint $table) {
            $table->index(['journal_id', 'date'], 'idx_daily_activities_journal_date');
            $table->index(['journal_id', 'is_approved'], 'idx_daily_activities_journal_approved');
        });

        // 3. Indeks performa pada tabel journal_assessments
        Schema::table('journal_assessments', function (Blueprint $table) {
            $table->index(['journal_id', 'assessment_id'], 'idx_ja_journal_assessment');
        });

        // 4. Indeks performa pada tabel weekly_approvals
        if (Schema::hasTable('weekly_approvals')) {
            Schema::table('weekly_approvals', function (Blueprint $table) {
                $table->index(['journal_id', 'week_number'], 'idx_wa_journal_week');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropIndex('idx_journals_student_phase');
            if (Schema::hasColumn('journals', 'kaprodi_status')) {
                $table->dropIndex('idx_journals_kaprodi_status');
            }
            $table->dropIndex('idx_journals_status');
        });

        Schema::table('daily_activities', function (Blueprint $table) {
            $table->dropIndex('idx_daily_activities_journal_date');
            $table->dropIndex('idx_daily_activities_journal_approved');
        });

        Schema::table('journal_assessments', function (Blueprint $table) {
            $table->dropIndex('idx_ja_journal_assessment');
        });

        if (Schema::hasTable('weekly_approvals')) {
            Schema::table('weekly_approvals', function (Blueprint $table) {
                $table->dropIndex('idx_wa_journal_week');
            });
        }
    }
};
