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
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Contoh: TKJ, RPL
            $table->string('name'); // Contoh: Teknik Komputer dan Jaringan
            $table->string('head_name')->nullable(); // Nama Kepala Program (Untuk Ttd)
            $table->string('head_nip')->nullable(); // NIP Kaprog
            $table->string('head_signature_path')->nullable(); // Path gambar Ttd Kaprog
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('majors');
    }
};
