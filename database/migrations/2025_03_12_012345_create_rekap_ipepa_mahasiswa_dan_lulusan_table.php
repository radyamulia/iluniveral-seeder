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
        Schema::create('rekap_ipepa_mahasiswa_dan_lulusan', function (Blueprint $table) {
            $table->id();
            $table->string('id_periode');
            $table->string('nama_periode');
            $table->string('rekap_jumlah_maba_regular');
            $table->string('rekap_jumlah_maba_transfer');
            $table->string('rekap_jumlah_mhs_aktif');
            $table->string('rekap_jumlah_lulusan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_ipepa_mahasiswa_dan_lulusan');
    }
};
