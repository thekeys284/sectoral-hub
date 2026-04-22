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
            $table->foreignId('opd_id')->constrained('opd')->onDelete('cascade')->nullable();
            $table->string('judul_kegiatan');
            $table->date('tahun_kegiatan');
            $table->string('nomor_rekomendasi')->nullable();
            $table->date('tgl_pengajuan')->nullable();
            $table->date('tgl_perbaikan_terakhir')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->string('lama_pemeriksaan')->nullable();
            $table->string('status_pengajuan')->nullable();
            $table->string('status_rekomendasi')->->nullable();
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
