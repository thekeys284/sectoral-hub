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
        Schema::create('sdsn', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sdsn');
            $table->string('nama_data');
            $table->string('konsep');
            $table->string('definisi');
            $table->string('klasifikasi_penyajian')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('satuan')->nullable();
            $table->date('tahun_penetapan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sdsn');
    }
};
