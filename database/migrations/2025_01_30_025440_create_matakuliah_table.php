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
        Schema::create('matakuliah', function (Blueprint $table) {
            $table->id();
            // foreign key to prodi table (uuid)
            $table->string('id_prodi', 100)->index();
            $table->foreign('id_prodi')->references('id_prodi')->on('prodi')->cascadeOnDelete();

            // foreign key to jenjang table (uuid)
            $table->integer('id_jenjang_didik')->index();
            $table->foreign('id_jenjang_didik')->references('id_jenjang_didik')->on('jenjang_pendidikan')->cascadeOnDelete();

            $table->string('id_matkul', 100);

            $table->string('kode_mata_kuliah', 20);
            $table->string('nama_mata_kuliah', 200);
            $table->decimal('sks_mata_kuliah', 5, 2)->default(0.00)->nullable();

            $table->string('id_jenis_mata_kuliah', 1)->nullable();
            $table->string('id_kelompok_mata_kuliah', 1)->nullable();
            $table->string('jns_mk', 1)->nullable();
            $table->string('kel_mk', 1)->nullable();

            $table->decimal('sks_tatap_muka', 5, 2)->default(0.00)->nullable();
            $table->decimal('sks_praktek', 5, 2)->default(0.00)->nullable();
            $table->decimal('sks_praktek_lapangan', 5, 2)->default(0.00)->nullable();
            $table->decimal('sks_simulasi', 5, 2)->default(0.00)->nullable();

            $table->integer('ada_sap')->nullable();
            $table->integer('ada_silabus')->nullable();
            $table->integer('ada_bahan_ajar')->nullable();
            $table->integer('ada_acara_praktek')->nullable();
            $table->integer('ada_diktat')->nullable();

            $table->date('tanggal_mulai_efektif')->format('d/m/Y')->nullable();
            $table->date('tanggal_selesai_efektif')->format('d/m/Y')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matakuliah');
    }
};
