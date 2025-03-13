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
        Schema::create('mahasiswa_lulus_do', function (Blueprint $table) {
            $table->id();
            // foreign key to id_prodi (uuid)
            $table->string('id_mahasiswa', 100)->index();
            $table->foreign('id_mahasiswa')->references('id_mahasiswa')->on('mahasiswa')->cascadeOnDelete();

            $table->string('id_registrasi_mahasiswa');
            $table->string('id_perguruan_tinggi', 100);
            $table->string('id_prodi', 100);
            $table->date('tgl_masuk_sp')->format('d/m/Y')->nullable();
            $table->date('tgl_keluar')->format('d/m/Y')->nullable();
            $table->string('skhun')->nullable();
            $table->string('no_peserta_ujian')->nullable();
            $table->string('no_seri_ijazah')->nullable();
            $table->date('tgl_create')->format('d/m/Y')->nullable();
            $table->string('sks_diakui')->nullable();
            $table->string('jalur_skripsi')->nullable();
            $table->string('judul_skripsi')->nullable();
            $table->string('bln_awal_bimbingan')->nullable();
            $table->string('bln_akhir_bimbingan')->nullable();
            $table->string('sk_yudisium')->nullable();
            $table->string('tgl_sk_yudisium')->nullable();
            $table->decimal('ipk', 4, 2)->nullable();
            $table->string('sert_prof')->nullable();
            $table->string('a_pindah_mhs_asing', 100)->nullable();
            $table->string('id_pt_asal', 100)->nullable();
            $table->string('id_prodi_asal', 100)->nullable();
            $table->string('nm_pt_asal')->nullable();
            $table->string('nm_prodi_asal')->nullable();
            $table->integer('id_jns_daftar')->nullable();
            $table->integer('id_jns_keluar')->nullable();
            $table->integer('id_jalur_masuk')->nullable();
            $table->integer('id_pembiayaan')->nullable();
            $table->string('id_minat_bidang')->nullable();
            $table->string('bidang_minor')->nullable();
            $table->decimal('biaya_masuk_kuliah', 10, 2)->nullable();
            $table->string('namapt')->nullable();
            $table->bigInteger('id_jur')->nullable();
            $table->string('nm_jns_daftar')->nullable();
            $table->string('nm_smt')->nullable();
            $table->string('nim')->unique();
            $table->string('nama_program_studi')->nullable();
            $table->string('angkatan')->nullable();
            $table->integer('id_jenis_keluar')->nullable();
            $table->string('nama_jenis_keluar', 40)->nullable();
            $table->date('tanggal_keluar')->format('d/m/Y')->nullable();
            $table->string('id_periode_keluar', 5)->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa_lulus_do');
    }
};
