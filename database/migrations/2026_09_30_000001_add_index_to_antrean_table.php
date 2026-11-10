<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Halaman antrean selalu memfilter dan mengurutkan berdasarkan tanggal serta status,
     * jadi kedua kolom ini perlu index.
     */
    public function up(): void
    {
        Schema::table('antrean', function (Blueprint $table) {
            $table->index('tanggal');
            $table->index('status');
            $table->index(['tanggal', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('antrean', function (Blueprint $table) {
            $table->dropIndex(['tanggal', 'status']);
            $table->dropIndex('status');
            $table->dropIndex('tanggal');
        });
    }
};
