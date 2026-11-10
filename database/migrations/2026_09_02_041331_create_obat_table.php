<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('obat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_obat')->unique();
            $table->string('nama_obat');
            $table->string('jenis_obat');
            $table->string('satuan');
            $table->decimal('harga', 12, 2);
            $table->integer('stok')->default(0);
            $table->date('tanggal_kedaluwarsa')->nullable();
            $table->enum('status', ['tersedia', 'habis', 'kedaluwarsa'])->default('tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};
