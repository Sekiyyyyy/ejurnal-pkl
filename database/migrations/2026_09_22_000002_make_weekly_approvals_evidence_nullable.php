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
        Schema::table('weekly_approvals', function (Blueprint $table) {
            $table->string('instructor_paraf')->nullable()->change();
            $table->string('instructor_live_photo')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_approvals', function (Blueprint $table) {
            $table->string('instructor_paraf')->nullable(false)->change();
            $table->string('instructor_live_photo')->nullable(false)->change();
        });
    }
};
