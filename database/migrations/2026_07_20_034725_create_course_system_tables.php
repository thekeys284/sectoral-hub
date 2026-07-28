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

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->text('lokasi_event')->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('category', ['pembinaan', 'sosialisasi','pelatihan','rapat'])->default('pembinaan');

            // link ke luar
            $table->string('meeting_link', 2048)->nullable();
            $table->string('link_materi', 2048)->nullable();
            $table->string('image_banner')->nullable();
            $table->string('virtual_bg')->nullable();

            // pengaturan absensi dan passing grade
            $table->dateTime('absensi_start')->nullable();
            $table->dateTime('absensi_end')->nullable();
            $table->integer('passing_grade')->default(70);
            $table->string('posttest_password')->nullable();

            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('event_questions', function (Blueprint $table){
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->enum('type',['pretest','posttest']);
            $table->text('question_text');
            $table->json('options');
            $table->string('correct_answer',2);
            $table->timestamps();
        });

        Schema::create('event_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('cascade');
            $table->text('question_text');
            $table->enum('type', ['scale', 'text'])->default('scale');
            $table->boolean('is_master')->default(false); // 1 = Pertanyaan Bawaan, 0 = Pertanyaan Khusus
            $table->boolean('is_active')->default(true);  // 1 = Dicentang/Aktif, 0 = Uncheck/Matikan
            $table->timestamps();
        });

        Schema::create('event_registrations', function (Blueprint $table){
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Rekap Presensi & Nilai
            $table->boolean('status_kehadiran')->default(false);
            $table->integer('score_pretest')->nullable();
            $table->integer('score_posttest')->nullable();

            $table->json('answers_pretest')->nullable();  
            $table->json('answers_posttest')->nullable();

            $table->string('sertifikat_path', 2048)->nullable();
            
            $table->timestamps();
        });

        Schema::create('event_evaluation_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('event_registrations')->onDelete('cascade');
            $table->foreignId('evaluation_id')->constrained('event_evaluations')->onDelete('cascade');
            $table->integer('rating')->nullable(); 
            $table->text('answer_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_evaluation_answers');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('event_evaluations');
        Schema::dropIfExists('event_questions');
        Schema::dropIfExists('events');
    }
};
