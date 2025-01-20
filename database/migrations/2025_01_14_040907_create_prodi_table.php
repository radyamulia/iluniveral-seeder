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
        Schema::create('prodi', function (Blueprint $table) {
            $table->id();
            $table->string('id_prodi', 100)->unique();
            $table->string('kode_program_studi', 10);
            $table->string('nama_program_studi', 100);
            $table->string('status', 1);
            $table->integer('id_jenjang_didik')->index();
            $table->foreign('id_jenjang_didik')->references('id_jenjang_didik')->on('jenjang_pendidikan')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prodi');
    }
};
