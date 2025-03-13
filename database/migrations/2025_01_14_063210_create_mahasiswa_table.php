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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('id_mahasiswa', 100)->unique();
            $table->string('id_registrasi_mahasiswa', 100);
            $table->string('id_sms', 100);
            $table->string('nim', 24)->nullable();
            $table->string('nama_mahasiswa', 100);
            $table->string('jenis_kelamin', 1);
            $table->date('tanggal_lahir')->format('d/m/Y');
            $table->double('ipk')->nullable();
            $table->string('nama_agama')->nullable();
            $table->string('nama_status_mahasiswa', 50)->nullable();
            $table->string('id_periode', 5)->nullable();
            $table->string('nama_periode_masuk', 50)->nullable();
            // foreign key to id_prodi (uuid)
            $table->string('id_prodi', 100)->index();
            $table->foreign('id_prodi')->references('id_prodi')->on('prodi')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
