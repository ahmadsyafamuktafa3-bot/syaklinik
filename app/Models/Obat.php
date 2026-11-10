<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    protected $table = 'obat';

    protected $fillable = [
        'kode_obat', 'nama_obat', 'jenis_obat', 'satuan',
        'harga', 'stok', 'tanggal_kedaluwarsa', 'status',
    ];

    public function riwayatStok(): HasMany
    {
        return $this->hasMany(StokHistory::class);
    }
}