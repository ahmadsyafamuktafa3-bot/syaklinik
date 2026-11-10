<?php

use App\Http\Controllers\AntreanController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\TindakanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'login_show']);
Route::get('/login', [LoginController::class, 'login_show'])->name('login');
Route::post('/login', [LoginController::class, 'login_aksi'])->name('login.aksi');
Route::post('/logout', [LoginController::class, 'login_keluar'])->name('logout');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard/admin', [DashboardController::class, 'dashboard_admin'])->name('dashboard.admin');

    Route::resource('pasien', PasienController::class)->only(['index']);

    Route::get('/pasien', [PasienController::class, 'pasien_data'])->name('pasien.data');
    Route::post('/pasien/simpan', [PasienController::class, 'pasien_simpan'])->name('pasien.simpan');
    Route::delete('/pasien/{pasien}/hapus', [PasienController::class, 'pasien_hapus'])->name('pasien.hapus');
    Route::get('/pasien/cetak', [PasienController::class, 'pasien_cetak'])->name('pasien.cetak');

    Route::get('/dokter', [DokterController::class, 'dokter_data'])->name('dokter.data');
    Route::post('/dokter/simpan', [DokterController::class, 'dokter_simpan'])->name('dokter.simpan');
    Route::delete('/dokter/{dokter}/hapus', [DokterController::class, 'dokter_hapus'])->name('dokter.hapus');

    Route::get('/tindakan', [TindakanController::class, 'tindakan_data'])->name('tindakan.data');
    Route::post('/tindakan/simpan', [TindakanController::class, 'tindakan_simpan'])->name('tindakan.simpan');
    Route::delete('/tindakan/{tindakan}/hapus', [TindakanController::class, 'tindakan_hapus'])->name('tindakan.hapus');

    Route::get('/obat', [ObatController::class, 'obat_data'])->name('obat.data');
    Route::post('/obat/simpan', [ObatController::class, 'obat_simpan'])->name('obat.simpan');
    Route::delete('/obat/{obat}/hapus', [ObatController::class, 'obat_hapus'])->name('obat.hapus');
    Route::post('/obat/{obat}/stok-masuk', [ObatController::class, 'obat_stok_masuk'])->name('obat.stok_masuk');
    Route::post('/obat/{obat}/stok-keluar', [ObatController::class, 'obat_stok_keluar'])->name('obat.stok_keluar');
    Route::get('/obat/riwayat-stok', [ObatController::class, 'obat_riwayat_stok'])->name('obat.riwayat_stok');

    Route::get('/jadwal', [JadwalController::class, 'jadwal_data'])->name('jadwal.data');
    Route::post('/jadwal/simpan', [JadwalController::class, 'jadwal_simpan'])->name('jadwal.simpan');
    Route::delete('/jadwal/{jadwal}/hapus', [JadwalController::class, 'jadwal_hapus'])->name('jadwal.hapus');

    Route::get('/pendaftaran', [PendaftaranController::class, 'pendaftaran_data'])->name('pendaftaran.data');
    Route::post('/pendaftaran/simpan', [PendaftaranController::class, 'pendaftaran_simpan'])->name('pendaftaran.simpan');
    Route::delete('/pendaftaran/{pendaftaran}/hapus', [PendaftaranController::class, 'pendaftaran_hapus'])->name('pendaftaran.hapus');

    Route::get('/antrean', [AntreanController::class, 'antrean_data'])->name('antrean.data');
    Route::post('/antrean/{id}/status', [AntreanController::class, 'antrean_status'])->name('antrean.status');
    Route::delete('/antrean/{id}/hapus', [AntreanController::class, 'antrean_hapus'])->name('antrean.hapus');

    Route::get('/pembayaran', [PembayaranController::class, 'pembayaran_data'])->name('pembayaran.data');
    Route::post('/pembayaran/buat/{pemeriksaan}', [PembayaranController::class, 'pembayaran_buat'])->name('pembayaran.buat');
    Route::get('/pembayaran/{pembayaran}/detail', [PembayaranController::class, 'pembayaran_detail'])->name('pembayaran.detail');
    Route::post('/pembayaran/{pembayaran}/bayar', [PembayaranController::class, 'pembayaran_bayar'])->name('pembayaran.bayar');
    Route::delete('/pembayaran/{pembayaran}/hapus', [PembayaranController::class, 'pembayaran_hapus'])->name('pembayaran.hapus');
    Route::get('/pembayaran/cetak', [PembayaranController::class, 'pembayaran_cetak'])->name('pembayaran.cetak');

    Route::get('/laporan', [LaporanController::class, 'laporan_data'])->name('laporan.data');
    Route::get('/laporan/cetak', [LaporanController::class, 'laporan_cetak'])->name('laporan.cetak');
});

Route::middleware(['auth', 'role:dokter'])->group(function () {
    Route::get('/dashboard/dokter', [DashboardController::class, 'dashboard_dokter'])->name('dashboard.dokter');

    Route::get('/pemeriksaan', [PemeriksaanController::class, 'pemeriksaan_data'])->name('pemeriksaan.data');
    Route::get('/pemeriksaan/antrean', [PemeriksaanController::class, 'pemeriksaan_antrean'])->name('pemeriksaan.antrean');
    Route::post('/pemeriksaan/simpan', [PemeriksaanController::class, 'pemeriksaan_simpan'])->name('pemeriksaan.simpan');
    Route::delete('/pemeriksaan/{pemeriksaan}/hapus', [PemeriksaanController::class, 'pemeriksaan_hapus'])->name('pemeriksaan.hapus');
    Route::post('/pemeriksaan/antrean/{id}/panggil', [PemeriksaanController::class, 'antrean_panggil'])->name('pemeriksaan.panggil');
    Route::post('/pemeriksaan/antrean/{id}/selesai', [PemeriksaanController::class, 'antrean_selesai'])->name('pemeriksaan.selesai');

    Route::post('/resep/simpan', [ResepController::class, 'resep_simpan'])->name('resep.simpan');
    Route::delete('/resep/{resep}/hapus', [ResepController::class, 'resep_hapus'])->name('resep.hapus');
});

Route::middleware(['auth', 'role:admin,dokter'])->group(function () {
    Route::get('/rekam-medis', [RekamMedisController::class, 'rekam_medis_data'])->name('rekam-medis.data');
    Route::get('/rekam-medis/{pasien}/detail', [RekamMedisController::class, 'rekam_medis_detail'])->name('rekam-medis.detail');

    Route::get('/resep', [ResepController::class, 'resep_data'])->name('resep.data');
    Route::post('/resep/{resep}/ditebus', [ResepController::class, 'resep_ditebus'])->name('resep.ditebus');
    Route::get('/resep/cetak', [ResepController::class, 'resep_cetak'])->name('resep.cetak');
});

Route::middleware(['auth', 'role:pasien'])->group(function () {
    Route::get('/dashboard/pasien', [DashboardController::class, 'dashboard_pasien'])->name('dashboard.pasien');

    Route::get('/pendaftaran/saya', [PendaftaranController::class, 'pendaftaran_saya'])->name('pendaftaran.saya');
    Route::get('/pendaftaran/daftar', [PendaftaranController::class, 'pendaftaran_daftar'])->name('pendaftaran.daftar');
    Route::post('/pendaftaran/daftar', [PendaftaranController::class, 'pendaftaran_pasien_simpan'])->name('pendaftaran.pasien_simpan');

    Route::get('/pembayaran/saya', [PembayaranController::class, 'pembayaran_saya'])->name('pembayaran.saya');
    Route::post('/pembayaran/{pembayaran}/saya-bayar', [PembayaranController::class, 'pembayaran_pasien_bayar'])->name('pembayaran.pasien_bayar');
});
