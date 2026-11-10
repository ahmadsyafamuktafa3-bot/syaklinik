<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    protected $table = 'rekam_medis';

    protected $fillable = [
        'pasien_id', 'pemeriksaan_id', 'tanggal',
        'keluhan', 'diagnosis', 'tindakan', 'catatan',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function pemeriksaan()
    {
        return $this->belongsTo(Pemeriksaan::class);
    }
}