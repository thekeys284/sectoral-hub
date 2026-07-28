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
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->text('lokasi_event')->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('category', ['pembinaan', 'sosialisasi', 'pelatihan', 'rapat'])->default('pembinaan');
            $table->string('meeting_link')->nullable();
            $table->string('link_materi')->nullable();
            $table->string('daftar_hadir')->nullable();
            $table->string('pretest')->nullable();
            $table->string('posttest')->nullable();
            $table->string('evaluasi')->nullable();
            $table->string('sertifikat')->nullable();
            $table->boolean('is_active')->default(true); 
            $table->string('image_banner')->nullable();
            $table->string('virtual_bg')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
