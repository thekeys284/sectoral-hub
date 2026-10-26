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
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'has_pretest')) {
                $table->boolean('has_pretest')->default(true);
            }
            if (!Schema::hasColumn('events', 'has_posttest')) {
                $table->boolean('has_posttest')->default(true);
            }
            if (!Schema::hasColumn('events', 'has_evaluasi')) {
                $table->boolean('has_evaluasi')->default(true);
            }
            if (!Schema::hasColumn('events', 'absensi_type')) {
                $table->enum('absensi_type', ['web', 'link'])->default('web');
            }
            if (!Schema::hasColumn('events', 'absensi_link')) {
                $table->string('absensi_link', 2048)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            foreach (['has_pretest', 'has_posttest', 'has_evaluasi', 'absensi_type', 'absensi_link'] as $col) {
                if (Schema::hasColumn('events', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

