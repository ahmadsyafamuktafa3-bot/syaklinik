<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obat')->cascadeOnDelete();
            $table->enum('jenis', ['masuk', 'keluar'])->default('masuk');
            $table->integer('jumlah');
            $table->string('keterangan')->nullable();
            $table->date('tanggal')->default(now()->toDateString());
            $table->unsignedBigInteger('pengguna_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_history');
    }
};