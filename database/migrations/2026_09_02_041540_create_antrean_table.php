<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('antrean', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pendaftaran_id')->unique();
            $table->integer('nomor_antrean');
            $table->date('tanggal');
            $table->enum('status', ['menunggu', 'dipanggil', 'diperiksa', 'selesai', 'batal'])->default('menunggu');
            $table->timestamps();

            $table->foreign('pendaftaran_id')->references('id')->on('pendaftaran')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrean');
    }
};
