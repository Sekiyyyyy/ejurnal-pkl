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
            $table->string('kaprodi_status')->default('PENDING'); // PENDING, WAITING_KAPROG, APPROVED, REJECTED
            $table->text('kaprodi_rejection_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn(['kaprodi_status', 'kaprodi_rejection_note']);
        });
    }
};
