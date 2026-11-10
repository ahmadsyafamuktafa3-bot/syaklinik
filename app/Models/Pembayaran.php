<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'nomor_pembayaran', 'pasien_id', 'pendaftaran_id', 'pemeriksaan_id',
        'tanggal', 'total', 'bayar', 'kembalian', 'status',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function pemeriksaan()
    {
        return $this->belongsTo(Pemeriksaan::class);
    }

    public function detail()
    {
        return $this->hasMany(DetailPembayaran::class);
    }
}