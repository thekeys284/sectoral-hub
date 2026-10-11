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
        // 1. Tambah kolom di tabel events jika belum ada
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'certificate_link')) {
                $table->string('certificate_link', 2048)->nullable();
            }
            if (!Schema::hasColumn('events', 'certificate_type')) {
                $table->enum('certificate_type', ['all', 'unique'])->default('all');
            }
        });

        // 2. Pastikan kolom sertifikat_path juga ada di tabel event_registrations
        if (Schema::hasTable('event_registrations')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                if (!Schema::hasColumn('event_registrations', 'sertifikat_path')) {
                    $table->string('sertifikat_path', 2048)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'certificate_type')) {
                $table->dropColumn('certificate_type');
            }
            if (Schema::hasColumn('events', 'certificate_link')) {
                $table->dropColumn('certificate_link');
            }
        });
    }
};
