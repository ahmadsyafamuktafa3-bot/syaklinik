<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    public function dokter_data(Request $request)
    {
        $keyword = $request->get('q');
        $status = $request->get('status');

        $dokter = Dokter::query()
            ->when($keyword, function ($query, $keyword) {
                $query->where('nama_dokter', 'like', "%$keyword%")
                    ->orWhere('kode_dokter', 'like', "%$keyword%")
                    ->orWhere('spesialisasi', 'like', "%$keyword%");
            })
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->orderBy('id', 'desc')
            ->paginate(10)->withQueryString();

        $usernames = Pengguna::where('role', 'dokter')
            ->whereIn('dokter_id', $dokter->pluck('id'))
            ->pluck('username', 'dokter_id');

        return view('dokter.data', compact('dokter', 'usernames'));
    }

    public function dokter_simpan(Request $request)
    {
        $request->validate([
            'kode_dokter'   => 'required|unique:dokter,kode_dokter,' . $request->id,
            'nama_dokter'   => 'required|string|max:255',
            'spesialisasi'  => 'required|string|max:100',
            'nomor_telepon' => 'required|string|max:20',
            'alamat'        => 'required|string',
            'status'        => 'required|in:aktif,tidak_aktif',
            'username'      => 'nullable|string|max:50',
            'password'      => 'nullable|string|min:6',
        ], [
            'kode_dokter.required' => 'Kode dokter wajib diisi.',
            'kode_dokter.unique'   => 'Kode dokter sudah digunakan.',
            'nama_dokter.required' => 'Nama dokter wajib diisi.',
            'spesialisasi.required' => 'Spesialisasi wajib diisi.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'alamat.required'      => 'Alamat wajib diisi.',
            'status.required'      => 'Status wajib dipilih.',
            'password.min'         => 'Password minimal 6 karakter.',
        ]);

        $dokter = Dokter::updateOrCreate(
            ['id' => $request->id],
            $request->only([
                'kode_dokter', 'nama_dokter', 'spesialisasi',
                'nomor_telepon', 'alamat', 'status',
            ])
        );

        if ($request->filled('username')) {
            $usernameDipakai = Pengguna::where('username', $request->username)
                ->where(function ($q) use ($dokter) {
                    $q->whereNull('dokter_id')->orWhere('dokter_id', '!=', $dokter->id);
                })
                ->exists();

            if ($usernameDipakai) {
                return back()->withErrors(['username' => 'Username sudah digunakan oleh akun lain.'])->withInput();
            }

            $akun = Pengguna::where('dokter_id', $dokter->id)->first();
            $data = [
                'username' => $request->username,
                'nama_lengkap' => $request->nama_dokter,
                'role' => 'dokter',
                'dokter_id' => $dokter->id,
            ];

            if ($request->filled('password')) {
                $data['password'] = $request->password;
            }

            if ($akun) {
                $akun->update($data);
            } else {
                if (!$request->filled('password')) {
                    return back()->withErrors(['password' => 'Password wajib diisi untuk membuat akun login baru.'])->withInput();
                }
                Pengguna::create($data);
            }
        }

        return redirect()->route('dokter.data')->with('success', 'Data dokter berhasil disimpan.');
    }

    public function dokter_hapus($id)
    {
        Dokter::findOrFail($id)->delete();
        return redirect()->route('dokter.data')->with('success', 'Data dokter berhasil dihapus.');
    }
}