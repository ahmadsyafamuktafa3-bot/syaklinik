<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\RekamMedis;
use Illuminate\Http\Request;

class RekamMedisController extends Controller
{
    public function rekam_medis_data(Request $request)
    {
        $keyword = $request->get('q');
        $dari = $request->get('dari');
        $sampai = $request->get('sampai');

        $rekamMedis = RekamMedis::with(['pasien', 'pemeriksaan.dokter'])
            ->when(auth()->user()->role === 'dokter', function ($query) {
                $query->whereHas('pemeriksaan', fn ($q) => $q->where('dokter_id', auth()->user()->dokter_id));
            })
            ->when($keyword, function ($query, $keyword) {
                $query->whereHas('pasien', function ($q) use ($keyword) {
                    $q->where('nama_pasien', 'like', "%$keyword%")
                        ->orWhere('nik', 'like', "%$keyword%")
                        ->orWhere('nomor_rekam_medis', 'like', "%$keyword%");
                });
            })
            ->when($dari, fn ($q, $dari) => $q->whereDate('tanggal', '>=', $dari))
            ->when($sampai, fn ($q, $sampai) => $q->whereDate('tanggal', '<=', $sampai))
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)->withQueryString();

        return view('rekam-medis.data', compact('rekamMedis'));
    }

    public function rekam_medis_detail($pasienId)
    {
        $pasien = Pasien::findOrFail($pasienId);

        $rekamMedis = RekamMedis::with('pemeriksaan.dokter', 'pemeriksaan.tindakans')
            ->where('pasien_id', $pasienId)
            ->when(auth()->user()->role === 'dokter', function ($query) {
                $query->whereHas('pemeriksaan', fn ($q) => $q->where('dokter_id', auth()->user()->dokter_id));
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('rekam-medis.detail', compact('pasien', 'rekamMedis'));
    }
}