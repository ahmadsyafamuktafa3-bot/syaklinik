<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
             $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->unsignedBigInteger('pasien_id');
            $table->unsignedBigInteger('pendaftaran_id')->nullable();
            $table->unsignedBigInteger('pemeriksaan_id')->nullable();
            $table->date('tanggal');
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('bayar', 12, 2)->default(0);
            $table->decimal('kembalian', 12, 2)->default(0);
            $table->enum('status', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->timestamps();

            $table->foreign('pasien_id')->references('id')->on('pasien')->onDelete('cascade');
            $table->foreign('pendaftaran_id')->references('id')->on('pendaftaran')->onDelete('set null');
            $table->foreign('pemeriksaan_id')->references('id')->on('pemeriksaan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
