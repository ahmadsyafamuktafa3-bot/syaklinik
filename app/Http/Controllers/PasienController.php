<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PasienController extends Controller
{
    public function pasien_data(request $request)
    {
        $keyword = $request->get('q');
        $pasien = Pasien::query()
            ->when($keyword, function ($query, $keyword){
                $query->where('nama_pasien', 'like', "%$keyword%")
            ->orwhere('nik', 'like', "%$keyword%")
            ->orwhere('nomor_rekam_medis', 'like', "%$keyword%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)->withQueryString();

        $usernames = Pengguna::where('role', 'pasien')
            ->whereIn('pasien_id', $pasien->pluck('id'))
            ->pluck('username', 'pasien_id');

        return view('pasien.data', compact('pasien', 'usernames'));
    }

    public function pasien_simpan(Request $request)
    {
         $request->validate([
            'nomor_rekam_medis' => 'required|unique:pasien,nomor_rekam_medis,' . $request->id,
            'nik'               => 'required|digits:16|unique:pasien,nik,' . $request->id,
            'nama_pasien'       => 'required|string|max:255',
            'jenis_kelamin'     => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir'     => 'required|date|before:today',
            'alamat'            => 'required|string',
            'nomor_telepon'     => 'required|string|max:20',
            'email'             => 'nullable|email',
            'username'          => 'nullable|string|max:50',
            'password'          => 'nullable|string|min:6',
        ], [
            'nomor_rekam_medis.required' => 'Nomor rekam medis wajib diisi.',
            'nomor_rekam_medis.unique'   => 'Nomor rekam medis sudah digunakan.',
            'nik.required'               => 'NIK wajib diisi.',
            'nik.digits'                 => 'NIK harus 16 digit.',
            'nik.unique'                 => 'NIK sudah terdaftar.',
            'nama_pasien.required'       => 'Nama pasien wajib diisi.',
            'jenis_kelamin.required'     => 'Jenis kelamin wajib dipilih.',
            'tanggal_lahir.required'     => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before'       => 'Tanggal lahir harus sebelum hari ini.',
            'alamat.required'            => 'Alamat wajib diisi.',
            'nomor_telepon.required'     => 'Nomor telepon wajib diisi.',
            'email.email'                => 'Format email tidak valid.',
            'password.min'               => 'Password minimal 6 karakter.',
        ]);

        $pasien = Pasien::updateOrCreate(
            ['id' => $request->id],
            $request->only([
                'nomor_rekam_medis', 'nik', 'nama_pasien', 'jenis_kelamin',
                'tanggal_lahir', 'alamat', 'nomor_telepon', 'email',
            ])
        );

        if ($request->filled('username')) {
            $usernameDipakai = Pengguna::where('username', $request->username)
                ->where(function ($q) use ($pasien) {
                    $q->whereNull('pasien_id')->orWhere('pasien_id', '!=', $pasien->id);
                })
                ->exists();

            if ($usernameDipakai) {
                return back()->withErrors(['username' => 'Username sudah digunakan oleh akun lain.'])->withInput();
            }

            $akun = Pengguna::where('pasien_id', $pasien->id)->first();
            $data = [
                'username' => $request->username,
                'nama_lengkap' => $request->nama_pasien,
                'email' => $request->email,
                'role' => 'pasien',
                'pasien_id' => $pasien->id,
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

        return redirect()->route('pasien.data')->with('success', 'Data pasien berhasil disimpan.');
    }

     public function pasien_hapus($id)
    {
        $pasien = Pasien::findOrFail($id);

        $terpakai = DB::table('pendaftaran')->where('pasien_id', $id)->exists();

        if ($terpakai) {
            return redirect()->route('pasien.data')->with('error', 'Pasien tidak dapat dihapus karena sudah memiliki riwayat pendaftaran.');
        }

        $pasien->delete();
        return redirect()->route('pasien.data')->with('success', 'Data pasien berhasil dihapus.');
    }

    public function pasien_cetak(Request $request)
    {
        $keyword = $request->get('q');
        $pasien = Pasien::query()
            ->when($keyword, function ($query, $keyword) {
                $query->where('nama_pasien', 'like', "%$keyword%")
                    ->orWhere('nik', 'like', "%$keyword%")
                    ->orWhere('nomor_rekam_medis', 'like', "%$keyword%");
            })
            ->orderBy('nama_pasien')->get();

        return view('pasien.cetak', compact('pasien', 'keyword'));
    }
}
