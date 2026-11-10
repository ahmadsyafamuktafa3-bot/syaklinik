<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    protected $table = 'dokter';

    protected $fillable = [
        'kode_dokter', 'nama_dokter', 'spesialisasi', 'nomor_telepon', 'alamat', 'status',
    ];
}