<?php

namespace App\Http\Controllers;

use App\Models\Antrean;
use App\Models\Obat;
use App\Models\Pemeriksaan;
use App\Models\Pendaftaran;
use App\Models\RekamMedis;
use App\Models\Tindakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemeriksaanController extends Controller
{
    public function pemeriksaan_data(Request $request)
    {
        $keyword = $request->get('q');
        $dari = $request->get('dari');
        $sampai = $request->get('sampai');

        $pemeriksaan = Pemeriksaan::with(['pasien', 'pendaftaran', 'tindakans'])
            ->where('dokter_id', auth()->user()->dokter_id)
            ->when($keyword, function ($query, $keyword) {
                $query->whereHas('pasien', fn ($q) => $q->where('nama_pasien', 'like', "%$keyword%"));
            })
            ->when($dari, fn ($q, $dari) => $q->whereDate('tanggal_pemeriksaan', '>=', $dari))
            ->when($sampai, fn ($q, $sampai) => $q->whereDate('tanggal_pemeriksaan', '<=', $sampai))
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)->withQueryString();

        $tindakanList = Tindakan::orderBy('nama_tindakan')->get();
        $pemeriksaanList = Pemeriksaan::with(['pasien'])
            ->where('dokter_id', auth()->user()->dokter_id)
            ->whereDoesntHave('resep')
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->get();
        $obatList = Obat::orderBy('nama_obat')->get();

        return view('pemeriksaan.data', compact('pemeriksaan', 'tindakanList', 'pemeriksaanList', 'obatList'));
    }

    public function pemeriksaan_antrean(Request $request)
    {
        $tanggalAntrean = $request->get('tanggal_antrean') ?: null;
        $qAntrean = $request->get('q_antrean') ?: null;
        $sembunyiSudahDiperiksa = $request->boolean('sembunyi_diperiksa');

        $antrean = Antrean::with(['pendaftaran.pasien', 'pendaftaran.jadwal', 'pendaftaran.pemeriksaan.tindakans'])
            ->when($tanggalAntrean, fn ($q, $tanggal) => $q->where('tanggal', $tanggal))
            ->where('status', '!=', 'batal')
            ->whereHas('pendaftaran', function ($query) use ($qAntrean, $sembunyiSudahDiperiksa) {
                $query->where('dokter_id', auth()->user()->dokter_id)
                    ->where('status', '!=', 'batal')
                    ->when($sembunyiSudahDiperiksa, fn ($sub) => $sub->whereDoesntHave('pemeriksaan'))
                    ->when($qAntrean, fn ($sub) => $sub->whereHas('pasien', fn ($p) => $p->where('nama_pasien', 'like', "%$qAntrean%")));
            })
            ->orderByDesc('tanggal')
            ->orderBy('nomor_antrean')
            ->paginate(15)->withQueryString();

        $tanggalList = Antrean::query()
            ->whereHas('pendaftaran', fn ($q) => $q->where('dokter_id', auth()->user()->dokter_id))
            ->distinct()
            ->orderByDesc('tanggal')
            ->limit(120)
            ->pluck('tanggal');

        $tindakanList = Tindakan::orderBy('nama_tindakan')->get();

        return view('pemeriksaan.antrean', compact('antrean', 'tanggalAntrean', 'qAntrean', 'sembunyiSudahDiperiksa', 'tanggalList', 'tindakanList'));
    }

    public function antrean_panggil($id)
    {
        $antrean = Antrean::with('pendaftaran')->findOrFail($id);

        if (! $antrean->pendaftaran || (int) $antrean->pendaftaran->dokter_id !== (int) auth()->user()->dokter_id) {
            abort(403, 'Antrean ini bukan antrean Anda.');
        }

        if ($antrean->pendaftaran->status === 'batal') {
            return redirect()->back()->with('error', 'Pendaftaran pasien ini sudah dibatalkan.');
        }

        $antrean->update(['status' => 'dipanggil']);

        return redirect()->back()->with('success', 'Pasien No. '.$antrean->nomor_antrean.' dipanggil.');
    }

    public function antrean_selesai($id)
    {
        $antrean = Antrean::with('pendaftaran')->findOrFail($id);

        if (! $antrean->pendaftaran || (int) $antrean->pendaftaran->dokter_id !== (int) auth()->user()->dokter_id) {
            abort(403, 'Antrean ini bukan antrean Anda.');
        }

        if ($antrean->status === 'batal') {
            return redirect()->back()->with('error', 'Antrean ini sudah dibatalkan.');
        }

        $antrean->update(['status' => 'selesai']);

        return redirect()->back()->with('success', 'Antrean No. '.$antrean->nomor_antrean.' ditandai selesai.');
    }

    public function pemeriksaan_simpan(Request $request)
    {
        $request->validate([
            'id' => 'nullable|integer|exists:pemeriksaan,id',
            'pendaftaran_id' => 'required|exists:pendaftaran,id',
            'tanggal_pemeriksaan' => 'required|date',
            'keluhan' => 'required|string',
            'pemeriksaan_fisik' => 'nullable|string',
            'diagnosis' => 'required|string',
            'tindakan_id' => 'nullable|array',
            'tindakan_id.*' => 'exists:tindakan,id',
            'catatan' => 'nullable|string',
        ], [
            'id.exists' => 'Data pemeriksaan tidak valid.',
            'pendaftaran_id.required' => 'Pasien wajib dipilih melalui antrean.',
            'tanggal_pemeriksaan.required' => 'Tanggal pemeriksaan wajib diisi.',
            'keluhan.required' => 'Keluhan wajib diisi.',
            'diagnosis.required' => 'Diagnosis wajib diisi.',
            'tindakan_id.*.exists' => 'Tindakan tidak valid.',
        ]);

        $pendaftaran = Pendaftaran::with('antrean')->findOrFail($request->pendaftaran_id);
        if ((int) $pendaftaran->dokter_id !== (int) auth()->user()->dokter_id) {
            abort(403, 'Pendaftaran ini bukan antrean Anda.');
        }

        if ($pendaftaran->status === 'batal') {
            return redirect()->back()->with('error', 'Pendaftaran pasien ini sudah dibatalkan.');
        }

        // id wajib milik dokter yang sedang login dan harusnya untuk pendaftaran yang sama,
        // kalau tidak dokter lain bisa menimpa hasil pemeriksaan orang lain.
        $id = $request->input('id');
        $pemeriksaanLama = $id ? Pemeriksaan::findOrFail($id) : null;

        if ($pemeriksaanLama && ((int) $pemeriksaanLama->dokter_id !== (int) auth()->user()->dokter_id
            || (int) $pemeriksaanLama->pendaftaran_id !== (int) $pendaftaran->id)) {
            abort(403, 'Data pemeriksaan ini bukan milik Anda.');
        }

        $pemeriksaan = DB::transaction(function () use ($request, $pendaftaran, $id) {
            $pemeriksaan = Pemeriksaan::updateOrCreate(
                ['id' => $id],
                [
                    'pendaftaran_id' => $pendaftaran->id,
                    'pasien_id' => $pendaftaran->pasien_id,
                    'dokter_id' => $pendaftaran->dokter_id,
                    'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
                    'keluhan' => $request->keluhan,
                    'pemeriksaan_fisik' => $request->pemeriksaan_fisik,
                    'diagnosis' => $request->diagnosis,
                    'catatan' => $request->catatan,
                ]
            );

            $tindakanData = [];
            foreach (($request->tindakan_id ?? []) as $tindakanId) {
                $tindakan = Tindakan::findOrFail($tindakanId);
                $tindakanData[$tindakanId] = ['biaya' => $tindakan->tarif];
            }
            $pemeriksaan->tindakans()->sync($tindakanData);

            RekamMedis::updateOrCreate(
                ['pemeriksaan_id' => $pemeriksaan->id],
                [
                    'pasien_id' => $pendaftaran->pasien_id,
                    'tanggal' => $request->tanggal_pemeriksaan,
                    'keluhan' => $request->keluhan,
                    'diagnosis' => $request->diagnosis,
                    'tindakan' => $pemeriksaan->tindakans->map(fn ($t) => $t->nama_tindakan)->implode(', '),
                    'catatan' => $request->catatan,
                ]
            );

            if ($pendaftaran->antrean && $pendaftaran->antrean->status !== 'batal') {
                $pendaftaran->antrean->update(['status' => 'diperiksa']);
            }

            return $pemeriksaan;
        });

        return redirect()->route('pemeriksaan.data')->with('success', 'Pemeriksaan berhasil disimpan.');
    }

    public function pemeriksaan_hapus($id)
    {
        $pemeriksaan = Pemeriksaan::with('pendaftaran.antrean')->findOrFail($id);
        if ((int) $pemeriksaan->dokter_id !== (int) auth()->user()->dokter_id) {
            abort(403, 'Data pemeriksaan ini bukan milik Anda.');
        }

        DB::transaction(function () use ($pemeriksaan) {
            RekamMedis::where('pemeriksaan_id', $pemeriksaan->id)->delete();
            $pemeriksaan->tindakans()->detach();
            $pemeriksaan->delete();

            // Kembalikan antrean supaya pasien bisa dipanggil lagi, kalau tidak
            // statusnya menggantung di "diperiksa" dan tombol Panggil hilang.
            $antrean = $pemeriksaan->pendaftaran?->antrean;
            if ($antrean && in_array($antrean->status, ['diperiksa', 'selesai'], true)) {
                $antrean->update([
                    'status' => $pemeriksaan->pendaftaran->status === 'batal' ? 'menunggu' : 'dipanggil',
                ]);
            }
        });

        return redirect()->route('pemeriksaan.data')->with('success', 'Pemeriksaan berhasil dihapus.');
    }
}
