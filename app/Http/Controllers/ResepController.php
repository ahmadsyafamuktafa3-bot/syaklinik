<?php

namespace App\Http\Controllers;

use App\Models\DetailPembayaran;
use App\Models\DetailResep;
use App\Models\Obat;
use App\Models\Pembayaran;
use App\Models\Pemeriksaan;
use App\Models\Resep;
use App\Models\StokHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepController extends Controller
{
    public function resep_data(Request $request)
    {
        $keyword = $request->get('q');
        $dari = $request->get('dari');
        $sampai = $request->get('sampai');
        $status = $request->get('status');

        $resep = Resep::with(['pasien', 'dokter', 'detail.obat'])
            ->when(auth()->user()->role === 'dokter', function ($query) {
                $query->where('dokter_id', auth()->user()->dokter_id);
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
            ->when($status, fn ($q, $status) => $q->where('status', $status))
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)->withQueryString();

        if (auth()->user()->role === 'dokter') {
            $pemeriksaanList = Pemeriksaan::with(['pasien'])
                ->where('dokter_id', auth()->user()->dokter_id)
                ->whereDoesntHave('resep')
                ->orderBy('tanggal_pemeriksaan', 'desc')
                ->get();
            $obatList = Obat::orderBy('nama_obat')->get();
        } else {
            $pemeriksaanList = collect();
            $obatList = collect();
        }

        return view('resep.data', compact('resep', 'pemeriksaanList', 'obatList'));
    }

    public function resep_simpan(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'catatan_resep' => 'nullable|string',
            'obat_id' => 'required|array|min:1',
            'obat_id.*' => 'exists:obat,id',
            'jumlah.*' => 'required|integer|min:1',
            'aturan_pakai.*' => 'nullable|string|max:255',
        ], [
            'tanggal.required' => 'Tanggal resep wajib diisi.',
            'obat_id.required' => 'Minimal satu obat wajib ditambahkan.',
            'obat_id.min' => 'Minimal satu obat wajib ditambahkan.',
            'obat_id.*.exists' => 'Obat tidak valid.',
            'jumlah.*.required' => 'Jumlah obat wajib diisi.',
            'jumlah.*.min' => 'Jumlah obat minimal 1.',
        ]);

        if ($request->has('id')) {
            $resepLama = Resep::with('detail')->findOrFail($request->id);
            if ($resepLama->dokter_id !== auth()->user()->dokter_id) {
                abort(403);
            }
            $pemeriksaanId = $resepLama->pemeriksaan_id;
        } else {
            $request->validate(['pemeriksaan_id' => 'required|exists:pemeriksaan,id'], [
                'pemeriksaan_id.required' => 'Pilih pemeriksaan pasien terlebih dahulu.',
            ]);
            $pemeriksaan = Pemeriksaan::with('resep')->findOrFail($request->pemeriksaan_id);
            if ($pemeriksaan->dokter_id !== auth()->user()->dokter_id) {
                abort(403);
            }
            if ($pemeriksaan->resep) {
                return back()->withErrors(['pemeriksaan_id' => 'Pemeriksaan ini sudah memiliki resep.'])->withInput();
            }
            $pemeriksaanId = $pemeriksaan->id;
        }

        $items = [];
        foreach ($request->obat_id as $i => $obatId) {
            $jumlah = (int) ($request->jumlah[$i] ?? 0);
            if ($obatId && $jumlah > 0) {
                $items[] = [
                    'obat_id' => (int) $obatId,
                    'jumlah' => $jumlah,
                    'aturan_pakai' => $request->aturan_pakai[$i] ?? null,
                ];
            }
        }

        $pemakaianLama = collect($resepLama->detail ?? [])
            ->groupBy('obat_id')
            ->map(fn ($g) => $g->sum('jumlah'));

        $pemakaianBaru = collect($items)
            ->groupBy('obat_id')
            ->map(fn ($g) => $g->sum('jumlah'));

        $kekurangan = [];
        foreach ($pemakaianBaru->keys()->merge($pemakaianLama->keys())->unique() as $obatId) {
            $selisih = ($pemakaianBaru[$obatId] ?? 0) - ($pemakaianLama[$obatId] ?? 0);
            if ($selisih > 0) {
                $obat = Obat::find($obatId);
                if (!$obat || $obat->stok < $selisih) {
                    $kekurangan[] = $obat->nama_obat ?? "Obat #$obatId";
                }
            }
        }

        if ($kekurangan) {
            return back()->withErrors(['obat_id' => 'Stok tidak mencukupi untuk: ' . implode(', ', $kekurangan) . '.'])->withInput();
        }

        DB::transaction(function () use ($request, $items, $pemakaianBaru, $pemakaianLama, $pemeriksaanId) {
            foreach ($pemakaianLama->keys()->merge($pemakaianBaru->keys())->unique() as $obatId) {
                $selisih = ($pemakaianBaru[$obatId] ?? 0) - ($pemakaianLama[$obatId] ?? 0);
                if ($selisih === 0) {
                    continue;
                }
                $obat = Obat::findOrFail($obatId);
                if ($selisih > 0) {
                    $obat->decrement('stok', $selisih);
                    StokHistory::create([
                        'obat_id' => $obat->id,
                        'jenis' => 'keluar',
                        'jumlah' => $selisih,
                        'keterangan' => 'Dipakai resep (pemeriksaan #' . $pemeriksaanId . ')',
                        'tanggal' => now()->toDateString(),
                        'pengguna_id' => auth()->id(),
                    ]);
                } else {
                    $obat->increment('stok', abs($selisih));
                    StokHistory::create([
                        'obat_id' => $obat->id,
                        'jenis' => 'masuk',
                        'jumlah' => abs($selisih),
                        'keterangan' => 'Pengembalian resep (pemeriksaan #' . $pemeriksaanId . ')',
                        'tanggal' => now()->toDateString(),
                        'pengguna_id' => auth()->id(),
                    ]);
                }
                $this->perbaruiStatusObat($obat);
            }

            $resep = Resep::updateOrCreate(
                ['id' => $request->id],
                [
                    'pemeriksaan_id' => $pemeriksaanId,
                    'pasien_id' => Pemeriksaan::findOrFail($pemeriksaanId)->pasien_id,
                    'dokter_id' => auth()->user()->dokter_id,
                    'tanggal' => $request->tanggal,
                    'catatan_resep' => $request->catatan_resep,
                    'status' => 'belum_ditebus',
                ]
            );

            DetailResep::where('resep_id', $resep->id)->delete();

            foreach ($items as $item) {
                $obat = Obat::findOrFail($item['obat_id']);
                $subtotal = $obat->harga * $item['jumlah'];
                DetailResep::create([
                    'resep_id' => $resep->id,
                    'obat_id' => $item['obat_id'],
                    'jumlah' => $item['jumlah'],
                    'aturan_pakai' => $item['aturan_pakai'],
                    'harga' => $obat->harga,
                    'subtotal' => $subtotal,
                ]);
            }
        });

        return redirect()->route('resep.data')->with('success', 'Resep berhasil disimpan.');
    }

    public function resep_ditebus($id)
    {
        $resep = Resep::with('pemeriksaan.tindakans', 'detail.obat')->findOrFail($id);

        if ($resep->status === 'ditebus') {
            return redirect()->route('resep.data')->with('info', 'Resep ini sudah ditebus sebelumnya.');
        }

        $resep->update(['status' => 'ditebus']);

        $pemeriksaan = $resep->pemeriksaan;
        if ($pemeriksaan && !$pemeriksaan->pembayaran) {
            $this->buatTagihanOtomatis($pemeriksaan);
        }

        return redirect()->route('resep.data')->with('success', 'Resep ditandai sudah ditebus dan tagihan otomatis dibuat untuk pasien.');
    }

    private function buatTagihanOtomatis(Pemeriksaan $pemeriksaan)
    {
        DB::transaction(function () use ($pemeriksaan) {
            $total = 0;
            $detail = [];

            foreach ($pemeriksaan->tindakans as $tindakan) {
                $harga = (float) ($tindakan->pivot->biaya ?? 0);
                $total += $harga;
                $detail[] = [
                    'jenis_pelayanan' => 'Tindakan',
                    'nama_pelayanan' => $tindakan->nama_tindakan,
                    'jumlah' => 1,
                    'harga' => $harga,
                    'subtotal' => $harga,
                ];
            }

            foreach ($pemeriksaan->resep->detail ?? [] as $item) {
                $subtotal = (float) $item->subtotal;
                $total += $subtotal;
                $detail[] = [
                    'jenis_pelayanan' => 'Obat',
                    'nama_pelayanan' => $item->obat->nama_obat ?? 'Obat',
                    'jumlah' => $item->jumlah,
                    'harga' => (float) $item->harga,
                    'subtotal' => $subtotal,
                ];
            }

            $pembayaran = Pembayaran::create([
                'nomor_pembayaran' => $this->generateNomorPembayaran(),
                'pasien_id' => $pemeriksaan->pasien_id,
                'pendaftaran_id' => $pemeriksaan->pendaftaran_id,
                'pemeriksaan_id' => $pemeriksaan->id,
                'tanggal' => now()->toDateString(),
                'total' => $total,
                'bayar' => 0,
                'kembalian' => 0,
                'status' => 'belum_lunas',
            ]);

            foreach ($detail as $item) {
                DetailPembayaran::create(array_merge(['pembayaran_id' => $pembayaran->id], $item));
            }
        });
    }

    private function generateNomorPembayaran()
    {
        $today = now()->format('Ymd');
        $count = Pembayaran::whereDate('tanggal', now()->toDateString())->count();

        do {
            $count++;
            $nomor = 'BYR-' . $today . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        } while (Pembayaran::where('nomor_pembayaran', $nomor)->exists());

        return $nomor;
    }

    public function resep_hapus($id)
    {
        $resep = Resep::with('detail')->findOrFail($id);
        if ($resep->dokter_id !== auth()->user()->dokter_id) {
            abort(403);
        }

        DB::transaction(function () use ($resep) {
            foreach ($resep->detail as $detail) {
                $obat = Obat::find($detail->obat_id);
                if ($obat) {
                    $obat->increment('stok', $detail->jumlah);
                    StokHistory::create([
                        'obat_id' => $obat->id,
                        'jenis' => 'masuk',
                        'jumlah' => $detail->jumlah,
                        'keterangan' => 'Pengembalian stok (resep #' . $resep->id . ')',
                        'tanggal' => now()->toDateString(),
                        'pengguna_id' => auth()->id(),
                    ]);
                    $this->perbaruiStatusObat($obat);
                }
            }
            $resep->detail()->delete();
            $resep->delete();
        });

        return redirect()->route('resep.data')->with('success', 'Resep berhasil dihapus dan stok dikembalikan.');
    }

    private function perbaruiStatusObat(Obat $obat)
    {
        if ($obat->stok <= 0) {
            $obat->update(['status' => 'habis']);
        } else {
            $obat->update(['status' => 'tersedia']);
        }
    }

    public function resep_cetak(Request $request)
    {
        $keyword = $request->get('q');
        $dari = $request->get('dari');
        $sampai = $request->get('sampai');
        $status = $request->get('status');

        $resep = Resep::with(['pasien', 'dokter', 'detail.obat'])
            ->when(auth()->user()->role === 'dokter', function ($query) {
                $query->where('dokter_id', auth()->user()->dokter_id);
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
            ->when($status, fn ($q, $status) => $q->where('status', $status))
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalHarga = $resep->sum(fn ($r) => $r->detail->sum('subtotal'));

        return view('resep.cetak', compact('resep', 'keyword', 'dari', 'sampai', 'totalHarga'));
    }
}