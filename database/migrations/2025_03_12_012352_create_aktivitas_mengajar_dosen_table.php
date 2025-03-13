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
        Schema::create('aktivitas_mengajar_dosen', function (Blueprint $table) {
            $table->id();
            
            $table->string('id_dosen', 100)->index();
            $table->foreign('id_dosen')->references('id_dosen')->on('dosen')->cascadeOnDelete();
            
            $table->string('id_prodi', 100)->index();
            $table->foreign('id_prodi')->references('id_prodi')->on('prodi')->cascadeOnDelete();
            
            $table->string('id_matkul', 100)->index();
            $table->foreign('id_matkul')->references('id_matkul')->on('matakuliah')->cascadeOnDelete();

            $table->string('id_registrasi_dosen');
            $table->string('id_periode');
            $table->string('nama_periode');
            $table->string('nama_mata_kuliah');
            $table->string('id_kelas');
            $table->string('nama_kelas_kuliah');
            $table->integer('rencana_minggu_pertemuan');
            $table->integer('realisasi_minggu_pertemuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktivitas_mengajar_dosen');
    }
};
