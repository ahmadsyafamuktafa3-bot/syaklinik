<?php

namespace App\Http\Controllers;

use App\Models\Antrean;
use App\Models\Dokter;
use App\Models\JadwalDokter;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\Pemeriksaan;
use App\Models\Pendaftaran;
use App\Models\RekamMedis;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard_admin()
    {
        $data = [
            'total_pasien' => Pasien::count(),
            'total_dokter' => Dokter::count(),
            'total_pendaftaran' => Pendaftaran::count(),
            'total_antrean' => Antrean::count(),
            // Hanya antrean hari ini; pendaftaran bisa dibuat untuk tanggal mendatang.
            'total_antrean_menunggu' => Antrean::where('status', 'menunggu')
                ->whereDate('tanggal', today())
                ->count(),
            'total_obat' => Obat::count(),
            'total_pembayaran' => Pembayaran::count(),
            'pendapatan' => Pembayaran::where('status', 'lunas')->sum('total'),
        ];

        return view('dashboard.admin', $data);
    }

    public function dashboard_dokter()
    {
        $dokter = Auth::user()->dokter_id;

        $jadwal = JadwalDokter::where('dokter_id', $dokter)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $antrean = Antrean::whereDate('tanggal', now()->toDateString())
            ->where('status', '!=', 'batal')
            ->whereHas('pendaftaran', fn ($q) => $q->where('dokter_id', $dokter))
            ->with('pendaftaran.pasien')
            ->orderBy('nomor_antrean')
            ->get();

        return view('dashboard.dokter', compact('jadwal', 'antrean'));
    }

    public function dashboard_pasien()
    {
        $pasien = Auth::user()->pasien_id;

        $pendaftaran = Pendaftaran::where('pasien_id', $pasien)
            ->with(['dokter', 'antrean'])
            ->orderBy('tanggal_kunjungan', 'desc')
            ->paginate(10);

        $riwayat = Pemeriksaan::where('pasien_id', $pasien)
            ->with('dokter')
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->paginate(10);

        $rekamMedis = RekamMedis::where('pasien_id', $pasien)
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('dashboard.pasien', compact('pendaftaran', 'riwayat', 'rekamMedis'));
    }
}
