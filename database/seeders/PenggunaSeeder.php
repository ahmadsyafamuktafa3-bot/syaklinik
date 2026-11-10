<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $dokter = Dokter::create([
            'kode_dokter' => 'D-001',
            'nama_dokter' => 'dr. Sari Rahmawati',
            'spesialisasi' => 'Umum',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Sleman, DIY',
            'status' => 'aktif',
        ]);

        $pasien = Pasien::create([
            'nomor_rekam_medis' => 'RM-0001',
            'nik' => '3400000000000001',
            'nama_pasien' => 'Budi Santoso',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2000-01-15',
            'alamat' => 'Gamping, Sleman',
            'nomor_telepon' => '082198765432',
        ]);

        Pengguna::create([
            'username' => 'admin',
            'nama_lengkap' => 'Administrator Klinik',
            'email' => 'admin@klinik.test',
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        Pengguna::create([
            'username' => 'dokter',
            'nama_lengkap' => $dokter->nama_dokter,
            'email' => 'dokter@klinik.test',
            'password' => 'dokter123',
            'role' => 'dokter',
            'dokter_id' => $dokter->id,
        ]);

        Pengguna::create([
            'username' => 'pasien',
            'nama_lengkap' => $pasien->nama_pasien,
            'email' => $pasien->email,
            'password' => 'pasien123',
            'role' => 'pasien',
            'pasien_id' => $pasien->id,
        ]);
    }
}