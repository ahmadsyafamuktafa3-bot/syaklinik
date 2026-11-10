<?php

namespace App\Http\Controllers;

use App\Models\DetailPembayaran;
use App\Models\Pembayaran;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function pembayaran_data(Request $request)
    {
        $keyword = $request->get('q');
        $dari = $request->get('dari');
        $sampai = $request->get('sampai');
        $status = $request->get('status');

        $pembayaran = Pembayaran::with(['pasien', 'detail'])
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('nomor_pembayaran', 'like', "%$keyword%")
                        ->orWhereHas('pasien', function ($p) use ($keyword) {
                            $p->where('nama_pasien', 'like', "%$keyword%")
                                ->orWhere('nik', 'like', "%$keyword%");
                        });
                });
            })
            ->when($dari, fn ($q, $dari) => $q->whereDate('tanggal', '>=', $dari))
            ->when($sampai, fn ($q, $sampai) => $q->whereDate('tanggal', '<=', $sampai))
            ->when($status, fn ($q, $status) => $q->where('status', $status))
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)->withQueryString();

        $pemeriksaan = Pemeriksaan::with(['pasien', 'dokter', 'tindakans', 'resep.detail.obat'])
            ->whereDoesntHave('pembayaran')
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('pembayaran.data', compact('pembayaran', 'pemeriksaan'));
    }

    public function pembayaran_buat($pemeriksaanId)
    {
        $pemeriksaan = Pemeriksaan::with(['pasien', 'tindakans', 'resep.detail.obat'])->findOrFail($pemeriksaanId);

        if ($pemeriksaan->pembayaran) {
            return redirect()->route('pembayaran.data')->with('info', 'Pemeriksaan ini sudah memiliki pembayaran.');
        }

        $pembayaran = DB::transaction(function () use ($pemeriksaan) {
            $total = 0;
            $detail = [];

            foreach ($pemeriksaan->tindakans as $tindakan) {
                $harga = (float) ($tindakan->pivot->biaya ?? 0);
                $subtotal = $harga;
                $total += $subtotal;
                $detail[] = [
                    'jenis_pelayanan' => 'Tindakan',
                    'nama_pelayanan' => $tindakan->nama_tindakan,
                    'jumlah' => 1,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
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

            return $pembayaran;
        });

        return redirect()->route('pembayaran.detail', $pembayaran->id)->with('success', 'Pembayaran dibuat. Silakan terima pembayaran dari pasien.');
    }

    public function pembayaran_detail($id)
    {
        $pembayaran = Pembayaran::with(['pasien', 'pemeriksaan.dokter', 'detail'])->findOrFail($id);
        return view('pembayaran.detail', compact('pembayaran'));
    }

    public function pembayaran_bayar(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $request->validate([
            'bayar' => 'required|numeric|min:0',
        ], [
            'bayar.required' => 'Jumlah uang diterima wajib diisi.',
        ]);

        $bayar = (float) $request->bayar;
        $total = (float) $pembayaran->total;

        if ($bayar < $total) {
            return back()->withErrors(['bayar' => 'Uang yang diterima kurang Rp ' . number_format($total - $bayar, 0, ',', '.') . '.'])->withInput();
        }

        $pembayaran->update([
            'bayar' => $bayar,
            'kembalian' => $bayar - $total,
            'status' => 'lunas',
        ]);

        return redirect()->route('pembayaran.data')->with('success', 'Pembayaran ' . $pembayaran->nomor_pembayaran . ' lunas. Kembalian: Rp ' . number_format($bayar - $total, 0, ',', '.'));
    }

    public function pembayaran_hapus($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        if ($pembayaran->status === 'lunas') {
            return back()->with('error', 'Pembayaran lunas tidak dapat dihapus.');
        }

        DB::transaction(function () use ($pembayaran) {
            $pembayaran->detail()->delete();
            $pembayaran->delete();
        });

        return redirect()->route('pembayaran.data')->with('success', 'Pembayaran berhasil dihapus.');
    }

    public function pembayaran_saya()
    {
        $pasienId = auth()->user()->pasien_id;

        $pembayaran = Pembayaran::with(['pemeriksaan.dokter', 'detail'])
            ->where('pasien_id', $pasienId)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $belumAdaTagihan = Pemeriksaan::with('dokter')
            ->where('pasien_id', $pasienId)
            ->whereDoesntHave('pembayaran')
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->get();

        return view('pembayaran.saya', compact('pembayaran', 'belumAdaTagihan'));
    }

    public function pembayaran_pasien_bayar(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        if ($pembayaran->pasien_id !== auth()->user()->pasien_id) {
            abort(403);
        }

        $request->validate([
            'bayar' => 'required|numeric|min:0',
        ], [
            'bayar.required' => 'Jumlah pembayaran wajib diisi.',
        ]);

        $bayar = (float) $request->bayar;
        $total = (float) $pembayaran->total;

        if ($bayar < $total) {
            return back()->withErrors(['bayar' => 'Jumlah pembayaran kurang Rp ' . number_format($total - $bayar, 0, ',', '.') . ' dari total ' . number_format($total, 0, ',', '.') . '.'])->withInput();
        }

        $pembayaran->update([
            'bayar' => $bayar,
            'kembalian' => $bayar - $total,
            'status' => 'lunas',
        ]);

        return redirect()->route('pembayaran.saya')->with('success', 'Pembayaran ' . $pembayaran->nomor_pembayaran . ' berhasil dilakukan. Kembalian: Rp ' . number_format($bayar - $total, 0, ',', '.'));
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

    public function pembayaran_cetak(Request $request)
    {
        $keyword = $request->get('q');
        $dari = $request->get('dari');
        $sampai = $request->get('sampai');
        $status = $request->get('status');

        $pembayaran = Pembayaran::with(['pasien', 'detail'])
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('nomor_pembayaran', 'like', "%$keyword%")
                        ->orWhereHas('pasien', function ($p) use ($keyword) {
                            $p->where('nama_pasien', 'like', "%$keyword%")
                                ->orWhere('nik', 'like', "%$keyword%");
                        });
                });
            })
            ->when($dari, fn ($q, $dari) => $q->whereDate('tanggal', '>=', $dari))
            ->when($sampai, fn ($q, $sampai) => $q->whereDate('tanggal', '<=', $sampai))
            ->when($status, fn ($q, $status) => $q->where('status', $status))
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalPembayaran = $pembayaran->where('status', 'lunas')->sum('total');

        return view('pembayaran.cetak', compact('pembayaran', 'keyword', 'dari', 'sampai', 'totalPembayaran'));
    }
}