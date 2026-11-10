<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPembayaran extends Model
{
    protected $table = 'detail_pembayaran';

    protected $fillable = [
        'pembayaran_id', 'jenis_pelayanan', 'nama_pelayanan',
        'jumlah', 'harga', 'subtotal',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }
}