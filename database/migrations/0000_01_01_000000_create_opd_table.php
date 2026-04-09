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
        Schema::create('opd', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('alamat')->nullable();
            $table->string('logo_path', 2048)->nullable();
            $table->string('email')->nullable();
            $table->string('no_kantor')->nullable();
            $table->unsignedBigInteger('pembina_id')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('opd');
    }
};
