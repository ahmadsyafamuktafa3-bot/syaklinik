<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('nama_lengkap');
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'dokter', 'pasien',]);
            $table->unsignedBigInteger('pasien_id')->nullable();
            $table->unsignedBigInteger('dokter_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('pasien_id')->references('id')->on('pasien')->onDelete('set null');
            $table->foreign('dokter_id')->references('id')->on('dokter')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
