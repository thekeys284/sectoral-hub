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
        Schema::create('daftar_data', function (Blueprint $table) {
            $table->id();
            $table->string('nama_data');
            $table->string('satuan')->nullable();
            $table->string('periode')->nullable();
            $table->string('kedalaman_kabkot')->nullable();
            $table->string('sifat_data')->nullable();
            $table->string('sumber_data')->nullable();
            $table->string('kode_sdsn')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('opd_id')->nullable();
            $table->foreignId('kegiatan_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_data');
    }
};
