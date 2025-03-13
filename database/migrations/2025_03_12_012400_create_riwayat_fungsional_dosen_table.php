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
        Schema::create('riwayat_fungsional_dosen', function (Blueprint $table) {
            $table->id();
            $table->string('id_dosen', 100)->index();
            $table->foreign('id_dosen')->references('id_dosen')->on('dosen')->onDelete('cascade');
            $table->string('nidn')->nullable();
            $table->string('nama_dosen');
            $table->string('id_jabatan_fungsional', 100);
            $table->string('nama_jabatan_fungsional');
            $table->string('sk_jabatan_fungsional');
            $table->date('mulai_sk_jabatan')->format('d/m/Y');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_fungsional_dosen');
    }
};
