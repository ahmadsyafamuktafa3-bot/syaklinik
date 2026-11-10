<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';

    protected $fillable = [
        'nomor_pendaftaran', 'pasien_id', 'dokter_id', 'jadwal_id',
        'tanggal_kunjungan', 'keluhan', 'status',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalDokter::class, 'jadwal_id');
    }

    public function antrean()
    {
        return $this->hasOne(Antrean::class);
    }

    public function pemeriksaan()
    {
        return $this->hasOne(Pemeriksaan::class);
    }

    public static function generateNomorPendaftaran()
    {
        $tanggal = now()->format('Ymd');
        $urutan = static::whereDate('created_at', today())->count() + 1;
        return 'DAFTAR-' . $tanggal . '-' . str_pad($urutan, 4, '0', STR_PAD_LEFT);
    }
}