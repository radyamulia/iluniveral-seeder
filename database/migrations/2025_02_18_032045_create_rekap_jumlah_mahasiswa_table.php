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
        Schema::create('rekap_jumlah_mahasiswa', function (Blueprint $table) {
            $table->id();
            // foreign key to id_prodi (uuid)
            $table->string('id_prodi', 100)->index();
            $table->foreign('id_prodi')->references('id_prodi')->on('prodi')->cascadeOnDelete();
            
            $table->string('id_periode', 100);
            $table->string('nama_periode', 50);

            $table->integer('aktif');
            $table->integer('cuti');
            $table->integer('non_aktif');
            $table->integer('sedang_double_degree');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_jumlah_mahasiswa');
    }
};
