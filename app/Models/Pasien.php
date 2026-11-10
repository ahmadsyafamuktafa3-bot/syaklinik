<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $table = 'pasien';

    protected $fillable = [
        'nomor_rekam_medis', 'nik', 'nama_pasien', 'jenis_kelamin',
        'tanggal_lahir', 'alamat', 'nomor_telepon', 'email',
    ];
}