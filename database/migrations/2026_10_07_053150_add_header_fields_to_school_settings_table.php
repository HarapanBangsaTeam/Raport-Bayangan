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
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('judul_laporan')->default('Laporan Hasil Belajar Tengah Semester 1 (Raport Bayangan)')->after('alamat');
            $table->string('tanggal_raport')->default('Karawang, 02 Oktober 2026')->after('nip_wali_kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['judul_laporan', 'tanggal_raport']);
        });
    }
};
