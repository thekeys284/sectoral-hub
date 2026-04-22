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
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');
            $table->date('periode_kegiatan')->nullable();
            $table->year('tahun_kegiatan');
            $table->string('cara_pengumpulan_data')->nullable();
            $table->string('data_utama')->nullable();
            $table->string('data_prioritas')->nullable();
            $table->string('aksesbilitas')->nullable();
            $table->foreignId('opd_id')->nullable()->constrained('opd')->onDelete('cascade');            
            $table->string('deskripsi')->nullable();
            $table->string('link_metadata_kegiatan')->nullable();
            $table->string('link_metadata_variabel')->nullable();
            $table->string('link_metadata_indikator')->nullable();
            $table->unsignedBigInteger('metadata_id')->nullable();
            $table->unsignedBigInteger('romantik_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};
