<?php

namespace Tests\Feature;

use App\Models\Dokter;
use App\Models\JadwalDokter;
use App\Models\Pasien;
use App\Models\Pemeriksaan;
use App\Models\Pendaftaran;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AntreanTampilOtomatisTest extends TestCase
{
    use RefreshDatabase;

    private function seedData(): void
    {
        $dokter = Dokter::create([
            'kode_dokter' => 'DK-001',
            'nama_dokter' => 'dr. Uji',
            'spesialisasi' => 'Umum',
            'nomor_telepon' => '0812',
            'alamat' => 'Alamat',
        ]);

        $jadwal = JadwalDokter::create([
            'dokter_id' => $dokter->id,
            'hari' => 'Senin',
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '12:00:00',
            'ruang' => 'R1',
            'kuota' => 10,
        ]);

        // Data antrean sengaja berjarak jauh dari hari ini.
        foreach (['2026-09-20', '2026-09-21'] as $i => $tanggal) {
            $pasien = Pasien::create([
                'nomor_rekam_medis' => 'RM-000' . $i,
                'nik' => '320000000000' . $i,
                'nama_pasien' => 'Pasien ' . $i,
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '1990-01-01',
                'alamat' => 'Alamat',
                'nomor_telepon' => '0812',
            ]);

            $pendaftaran = Pendaftaran::create([
                'nomor_pendaftaran' => 'P-2026-09-20-000' . $i,
                'pasien_id' => $pasien->id,
                'dokter_id' => $dokter->id,
                'jadwal_id' => $jadwal->id,
                'tanggal_kunjungan' => $tanggal,
                'keluhan' => 'Demam',
                'status' => 'terdaftar',
            ]);

            \App\Models\Antrean::create([
                'pendaftaran_id' => $pendaftaran->id,
                'nomor_antrean' => $i + 1,
                'tanggal' => $tanggal,
                'status' => 'menunggu',
            ]);
        }
    }

    public function test_admin_default_menampilkan_semua_antrean_tanpa_harus_cari(): void
    {
        $this->seedData();
        $admin = Pengguna::create(['username' => 'admin', 'nama_lengkap' => 'Admin', 'password' => bcrypt('rahasia'), 'role' => 'admin']);

        $res = $this->actingAs($admin)->get('/antrean');

        $res->assertOk();
        $res->assertSee('Pasien 0');
        $res->assertSee('Pasien 1');
        $res->assertSee('2 data');
        $res->assertSee('Semua Tanggal', false);
    }

    public function test_admin_bisa_filter_tanggal(): void
    {
        $this->seedData();
        $admin = Pengguna::create(['username' => 'admin', 'nama_lengkap' => 'Admin', 'password' => bcrypt('rahasia'), 'role' => 'admin']);

        $res = $this->actingAs($admin)->get('/antrean?tanggal=2026-09-20');

        $res->assertOk();
        $res->assertSee('Pasien 0');
        $res->assertDontSee('Pasien 1');
        $res->assertSee('1 data');
    }

    public function test_dokter_default_menampilkan_semua_antrean(): void
    {
        $this->seedData();
        $dokter = Dokter::first();
        $dok = Pengguna::create(['username' => 'dok', 'nama_lengkap' => 'Dok', 'password' => bcrypt('rahasia'), 'role' => 'dokter', 'dokter_id' => $dokter->id]);

        $res = $this->actingAs($dok)->get('/pemeriksaan/antrean');

        $res->assertOk();
        $res->assertSee('Pasien 0');
        $res->assertSee('Pasien 1');
        $res->assertSee('Semua Tanggal', false);
    }

    public function test_dokter_bisa_sembunyikan_yang_sudah_diperiksa(): void
    {
        $this->seedData();
        $dokter = Dokter::first();
        $dok = Pengguna::create(['username' => 'dok', 'nama_lengkap' => 'Dok', 'password' => bcrypt('rahasia'), 'role' => 'dokter', 'dokter_id' => $dokter->id]);

        $terperiksa = Pendaftaran::where('nomor_pendaftaran', 'P-2026-09-20-0000')->first();
        Pemeriksaan::create([
            'pendaftaran_id' => $terperiksa->id,
            'pasien_id' => $terperiksa->pasien_id,
            'dokter_id' => $dokter->id,
            'tanggal_pemeriksaan' => '2026-09-20',
            'keluhan' => 'Demam',
            'diagnosis' => 'Demam Biasa',
        ]);

        $res = $this->actingAs($dok)->get('/pemeriksaan/antrean');
        $res->assertOk();
        $res->assertSee('Sudah Diperiksa');
        $res->assertSee('2 data');

        $res = $this->actingAs($dok)->get('/pemeriksaan/antrean?sembunyi_diperiksa=1');
        $res->assertOk();
        $res->assertDontSee('Sudah Diperiksa');
        $res->assertSee('Pasien 1');
        $res->assertDontSee('Pasien 0');
        $res->assertSee('1 data');
    }
}

