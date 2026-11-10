<?php

namespace App\Http\Controllers;

use App\Models\Antrean;
use App\Models\Dokter;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\Pemeriksaan;
use App\Models\Pendaftaran;
use App\Models\RekamMedis;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    protected $jenisList = [
        'pasien' => 'Data Pasien',
        'dokter' => 'Data Dokter',
        'pendaftaran' => 'Pendaftaran Pasien',
        'antrean' => 'Antrean',
        'pemeriksaan' => 'Pemeriksaan',
        'rekam_medis' => 'Rekam Medis',
        'obat' => 'Obat',
        'pembayaran' => 'Pembayaran',
    ];

    public function laporan_data(Request $request)
    {
        $jenis = $request->get('jenis', 'pendaftaran');
        $dari = $request->get('dari');
        $sampai = $request->get('sampai');

        [$rows, $ringkasan] = $this->ambilLaporan($jenis, $dari, $sampai);

        return view('laporan.index', [
            'jenis' => $jenis,
            'jenisList' => $this->jenisList,
            'dari' => $dari,
            'sampai' => $sampai,
            'rows' => $rows,
            'ringkasan' => $ringkasan,
        ]);
    }

    public function laporan_cetak(Request $request)
    {
        $jenis = $request->get('jenis', 'pendaftaran');
        $dari = $request->get('dari');
        $sampai = $request->get('sampai');

        [$rows, $ringkasan] = $this->ambilLaporan($jenis, $dari, $sampai);

        return view('laporan.cetak', [
            'jenis' => $jenis,
            'jenisList' => $this->jenisList,
            'dari' => $dari,
            'sampai' => $sampai,
            'rows' => $rows,
            'ringkasan' => $ringkasan,
        ]);
    }

    private function ambilLaporan(string $jenis, ?string $dari, ?string $sampai)
    {
        $ringkasan = ['total_baris' => 0, 'total_biaya' => 0];

        switch ($jenis) {
            case 'pasien':
                $rows = Pasien::when($dari, fn ($q, $dari) => $q->whereDate('created_at', '>=', $dari))
                    ->when($sampai, fn ($q, $sampai) => $q->whereDate('created_at', '<=', $sampai))
                    ->orderBy('nama_pasien')->get();
                break;

            case 'dokter':
                $rows = Dokter::orderBy('nama_dokter')->get();
                break;

            case 'pendaftaran':
                $rows = Pendaftaran::with(['pasien', 'dokter', 'jadwal'])
                    ->when($dari, fn ($q, $dari) => $q->whereDate('tanggal_kunjungan', '>=', $dari))
                    ->when($sampai, fn ($q, $sampai) => $q->whereDate('tanggal_kunjungan', '<=', $sampai))
                    ->orderBy('tanggal_kunjungan', 'desc')->get();
                break;

            case 'antrean':
                $rows = Antrean::with(['pendaftaran.pasien', 'pendaftaran.jadwal.dokter'])
                    ->when($dari, fn ($q, $dari) => $q->whereDate('tanggal', '>=', $dari))
                    ->when($sampai, fn ($q, $sampai) => $q->whereDate('tanggal', '<=', $sampai))
                    ->orderBy('tanggal', 'desc')->get();
                break;

            case 'pemeriksaan':
                $rows = Pemeriksaan::with(['pasien', 'dokter', 'tindakans'])
                    ->when($dari, fn ($q, $dari) => $q->whereDate('tanggal_pemeriksaan', '>=', $dari))
                    ->when($sampai, fn ($q, $sampai) => $q->whereDate('tanggal_pemeriksaan', '<=', $sampai))
                    ->orderBy('tanggal_pemeriksaan', 'desc')->get();
                break;

            case 'rekam_medis':
                $rows = RekamMedis::with('pasien')
                    ->when($dari, fn ($q, $dari) => $q->whereDate('tanggal', '>=', $dari))
                    ->when($sampai, fn ($q, $sampai) => $q->whereDate('tanggal', '<=', $sampai))
                    ->orderBy('tanggal', 'desc')->get();
                break;

            case 'obat':
                $rows = Obat::orderBy('nama_obat')->get();
                break;

            case 'pembayaran':
            default:
                $jenis = 'pembayaran';
                $rows = Pembayaran::with(['pasien', 'detail'])
                    ->when($dari, fn ($q, $dari) => $q->whereDate('tanggal', '>=', $dari))
                    ->when($sampai, fn ($q, $sampai) => $q->whereDate('tanggal', '<=', $sampai))
                    ->where('status', 'lunas')
                    ->orderBy('tanggal', 'desc')->get();
                $ringkasan['total_biaya'] = $rows->sum('total');
                break;
        }

        $ringkasan['total_baris'] = $rows->count();

        return [$rows, $ringkasan];
    }
}