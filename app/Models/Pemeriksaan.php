<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemeriksaan extends Model
{
    protected $table = 'pemeriksaan';

    protected $fillable = [
        'pendaftaran_id', 'pasien_id', 'dokter_id', 'tanggal_pemeriksaan',
        'keluhan', 'pemeriksaan_fisik', 'diagnosis', 'catatan',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function tindakans()
    {
        return $this->belongsToMany(Tindakan::class, 'pemeriksaan_tindakan')
            ->withPivot('biaya')->withTimestamps();
    }

    public function resep()
    {
        return $this->hasOne(Resep::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}