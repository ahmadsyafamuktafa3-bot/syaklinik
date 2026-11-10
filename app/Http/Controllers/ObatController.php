<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\StokHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObatController extends Controller
{
    public function obat_data(Request $request)
    {
        $keyword = $request->get('q');
        $status = $request->get('status');

        $obat = Obat::query()
            ->when($keyword, function ($query, $keyword) {
                $query->where('nama_obat', 'like', "%$keyword%")
                    ->orWhere('kode_obat', 'like', "%$keyword%");
            })
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->orderBy('id', 'desc')
            ->paginate(10)->withQueryString();

        return view('obat.data', compact('obat'));
    }

    public function obat_simpan(Request $request)
    {
        $obat = $request->id ? Obat::find($request->id) : null;

        $request->validate([
            'kode_obat' => 'required|unique:obat,kode_obat,' . $request->id,
            'nama_obat' => 'required|string|max:255',
            'jenis_obat' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'harga' => 'required|numeric|min:0',
            'tanggal_kedaluwarsa' => 'required|date|after:today',
            'status' => 'required|in:tersedia,habis,kedaluwarsa',
        ], [
            'kode_obat.required' => 'Kode obat wajib diisi.',
            'kode_obat.unique' => 'Kode obat sudah digunakan.',
            'nama_obat.required' => 'Nama obat wajib diisi.',
            'jenis_obat.required' => 'Jenis obat wajib diisi.',
            'satuan.required' => 'Satuan wajib diisi.',
            'harga.required' => 'Harga wajib diisi.',
            'tanggal_kedaluwarsa.required' => 'Tanggal kedaluwarsa wajib diisi.',
            'tanggal_kedaluwarsa.after' => 'Tanggal kedaluwarsa harus setelah hari ini.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        $data = $request->only([
            'kode_obat', 'nama_obat', 'jenis_obat', 'satuan',
            'harga', 'tanggal_kedaluwarsa', 'status',
        ]);

        // Stok awal otomatis 0; penambahan/pengurangan hanya lewat Stok Masuk / Stok Keluar.
        $data['stok'] = $obat?->stok ?? 0;

        if ($data['stok'] <= 0) {
            $data['status'] = 'habis';
        }

        Obat::updateOrCreate(['id' => $request->id], $data);

        return redirect()->route('obat.data')->with('success', 'Data obat berhasil disimpan. Stok awal otomatis 0.');
    }

    public function obat_stok_masuk(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'jumlah.required' => 'Jumlah stok masuk wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah minimal 1.',
        ]);

        DB::transaction(function () use ($obat, $request) {
            $obat->increment('stok', $request->jumlah);
            $this->perbaruiStatusObat($obat);
            StokHistory::create([
                'obat_id' => $obat->id,
                'jenis' => 'masuk',
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'tanggal' => now()->toDateString(),
                'pengguna_id' => auth()->id(),
            ]);
        });

        return redirect()->route('obat.data')->with('success', 'Stok masuk: ' . $obat->nama_obat . ' bertambah ' . $request->jumlah . ' ' . $obat->satuan . '.');
    }

    public function obat_stok_keluar(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'jumlah.required' => 'Jumlah stok keluar wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah minimal 1.',
        ]);

        if ($obat->stok < $request->jumlah) {
            return back()->withErrors(['jumlah' => 'Stok tidak mencukupi. Sisa stok saat ini: ' . $obat->stok . ' ' . $obat->satuan . '.'])->withInput();
        }

        DB::transaction(function () use ($obat, $request) {
            $obat->decrement('stok', $request->jumlah);
            $this->perbaruiStatusObat($obat);
            StokHistory::create([
                'obat_id' => $obat->id,
                'jenis' => 'keluar',
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'tanggal' => now()->toDateString(),
                'pengguna_id' => auth()->id(),
            ]);
        });

        return redirect()->route('obat.data')->with('success', 'Stok keluar: ' . $obat->nama_obat . ' berkurang ' . $request->jumlah . ' ' . $obat->satuan . '.');
    }

    public function obat_riwayat_stok(Request $request)
    {
        $keyword = $request->get('q');
        $jenis = $request->get('jenis');

        $riwayat = StokHistory::with('obat', 'pengguna')
            ->when($keyword, function ($query, $keyword) {
                $query->whereHas('obat', function ($q) use ($keyword) {
                    $q->where('nama_obat', 'like', "%$keyword%")
                        ->orWhere('kode_obat', 'like', "%$keyword%");
                });
            })
            ->when($jenis, fn ($query, $jenis) => $query->where('jenis', $jenis))
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)->withQueryString();

        return view('obat.riwayat', compact('riwayat', 'keyword', 'jenis'));
    }

    public function obat_hapus($id)
    {
        Obat::findOrFail($id)->delete();
        return redirect()->route('obat.data')->with('success', 'Data obat berhasil dihapus.');
    }

    private function perbaruiStatusObat(Obat $obat)
    {
        $obat->update([
            'status' => $obat->stok <= 0 ? 'habis' : 'tersedia',
        ]);
    }
}