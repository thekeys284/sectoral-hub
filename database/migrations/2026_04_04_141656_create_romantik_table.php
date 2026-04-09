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
        Schema::create('romantik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opd_id')->constrained('opd')->onDelete('cascade');
            $table->string('judul_kegiatan');
            $table->date('tahun_kegiatan');
            $table->string('nomor_rekomendasi');
            $table->date('tgl_pengajuan');
            $table->date('tgl_perbaikan_terakhir');
            $table->date('tgl_selesai');
            $table->string('lama_pemeriksaan');
            $table->enum('status_pengajuan', ['pemeriksaan', 'penerbitan', 'pengesahan', 'perbaikan', 'selesai'])->default('pemeriksaan');
            $table->enum('status_rekomendasi', ['dibatalkan', 'ditolak', 'layak'])->default('layak');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('romantik');
    }
};
