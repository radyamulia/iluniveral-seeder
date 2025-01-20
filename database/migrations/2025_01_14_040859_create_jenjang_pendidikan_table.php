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
        Schema::create('jenjang_pendidikan', function (Blueprint $table) {
            $table->id();
            $table->integer('id_jenjang_didik')->unique();
            $table->string('nama_jenjang_didik', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenjang_pendidikan');
    }
};
