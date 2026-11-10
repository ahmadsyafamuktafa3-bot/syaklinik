<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\JadwalDokter;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function jadwal_data(Request $request)
    {
        $keyword = $request->get('q');
        $hari = $request->get('hari');

        $jadwal = JadwalDokter::with('dokter')
            ->when($keyword, function ($query, $keyword) {
                $query->whereHas('dokter', function ($q) use ($keyword) {
                    $q->where('nama_dokter', 'like', "%$keyword%")
                        ->orWhere('kode_dokter', 'like', "%$keyword%");
                })->orWhere('ruang', 'like', "%$keyword%");
            })
            ->when($hari, fn ($query, $hari) => $query->where('hari', $hari))
            ->orderBy('dokter_id')
            ->orderBy('jam_mulai')
            ->paginate(15)->withQueryString();

        $dokterList = Dokter::where('status', 'aktif')->orderBy('nama_dokter')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('jadwal.data', compact('jadwal', 'dokterList', 'hariList'));
    }

    public function jadwal_simpan(Request $request)
    {
        $isEdit = $request->has('id') && $request->filled('id');

        $hariRules = $isEdit
            ? ['hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu']
            : ['hari' => 'required|array|min:1', 'hari.*' => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'];

        $request->validate(array_merge([
            'dokter_id' => 'required|exists:dokter,id',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'ruang' => 'required|string|max:50',
            'kuota' => 'required|integer|min:1',
        ], $hariRules), [
            'dokter_id.required' => 'Dokter wajib dipilih.',
            'dokter_id.exists' => 'Dokter tidak valid.',
            'hari.required' => 'Minimal satu hari wajib dipilih.',
            'hari.array' => 'Pilihan hari tidak valid.',
            'hari.min' => 'Minimal satu hari wajib dipilih.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'ruang.required' => 'Ruang wajib diisi.',
            'kuota.required' => 'Kuota wajib diisi.',
            'kuota.min' => 'Kuota minimal 1.',
        ]);

        $hariList = $isEdit ? [$request->hari] : $request->hari;
        $kolom = $request->only(['dokter_id', 'jam_mulai', 'jam_selesai', 'ruang', 'kuota']);

        foreach ($hariList as $hari) {
            $conflict = JadwalDokter::where('dokter_id', $request->dokter_id)
                ->where('hari', $hari)
                ->where('id', '!=', $request->id)
                ->where(function ($q) use ($request) {
                    $q->where('jam_mulai', '<', $request->jam_selesai)
                        ->where('jam_selesai', '>', $request->jam_mulai);
                })
                ->exists();

            if ($conflict) {
                return back()->withErrors([
                    'hari' => "Jadwal bentrok hari {$hari}! Dokter ini sudah memiliki jadwal pada jam yang bersinggungan di hari tersebut.",
                ])->withInput();
            }
        }

        foreach ($hariList as $hari) {
            if ($isEdit) {
                JadwalDokter::where('id', $request->id)->update(array_merge($kolom, ['hari' => $hari]));
            } else {
                JadwalDokter::create(array_merge($kolom, ['hari' => $hari]));
            }
        }

        $pesan = $isEdit
            ? 'Jadwal dokter berhasil diubah.'
            : count($hariList) . ' jadwal dokter berhasil disimpan.';

        return redirect()->route('jadwal.data')->with('success', $pesan);
    }

    public function jadwal_hapus($id)
    {
        JadwalDokter::findOrFail($id)->delete();
        return redirect()->route('jadwal.data')->with('success', 'Jadwal dokter berhasil dihapus.');
    }
}