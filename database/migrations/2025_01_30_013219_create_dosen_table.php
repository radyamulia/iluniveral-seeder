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
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->string('id_dosen', 100);
            $table->string('nama_dosen', 200);
            $table->string('nidn', 10)->nullable();
            $table->string('nip', 18)->nullable();
            $table->string('jenis_kelamin', 1);
            $table->string('nama_agama', 50);
            $table->date('tanggal_lahir')->format('d/m/Y');
            $table->string('nama_status_aktif', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
