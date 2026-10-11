<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('master_variable', function (Blueprint $table) {
//             $table->id();
//             $table->string('id_variable');
//             $table->string('type');
//             $table->foreignId('id_karakteristik')->nullable()->constrained('master_karakteristik');
//             $table->foreignId('id_judul_baris')->nullable();
//             $table->foreignId('id_periode_waktu');
//             $table->foreignId('id_satuan')->nullable();
//             $table->foreignId('id_label');
//             $table->foreignId('id_opd');
//             $table->text('deskripsi')->nullable();
//             $table->string('keterangan_tambahan')->nullable();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('master_variable');
//     }
// };


