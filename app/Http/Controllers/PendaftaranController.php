<?php

namespace App\Http\Controllers;

use App\Models\Antrean;
use App\Models\JadwalDokter;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    public function pendaftaran_data(Request $request)
    {
        $keyword = $request->get('q');
        $tanggal = $request->get('tanggal');
        $status = $request->get('status');

        $pendaftaran = Pendaftaran::with(['pasien', 'dokter', 'jadwal', 'antrean'])
            ->when($keyword, function ($query, $keyword) {
                $query->where('nomor_pendaftaran', 'like', "%$keyword%")
                    ->orWhereHas('pasien', fn ($q) => $q->where('nama_pasien', 'like', "%$keyword%"))
                    ->orWhereHas('dokter', fn ($q) => $q->where('nama_dokter', 'like', "%$keyword%"));
            })
            ->when($tanggal, fn ($q, $tanggal) => $q->where('tanggal_kunjungan', $tanggal))
            ->when($status, fn ($q, $status) => $q->where('status', $status))
            ->orderBy('tanggal_kunjungan', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)->withQueryString();

        $pasienList = Pasien::orderBy('nama_pasien')->get();
        $jadwalList = JadwalDokter::with('dokter')
            ->whereHas('dokter', fn ($q) => $q->where('status', 'aktif'))
            ->orderBy('hari')->orderBy('jam_mulai')->get();

        return view('pendaftaran.data', compact('pendaftaran', 'pasienList', 'jadwalList'));
    }

    public function pendaftaran_simpan(Request $request)
    {
        $request->validate([
            'pasien_id' => 'required|exists:pasien,id',
            'jadwal_id' => 'required|exists:jadwal_dokter,id',
            'tanggal_kunjungan' => 'required|date|after_or_equal:today',
            'keluhan' => 'required|string|max:1000',
        ], [
            'pasien_id.required' => 'Pasien wajib dipilih.',
            'jadwal_id.required' => 'Jadwal dokter wajib dipilih.',
            'tanggal_kunjungan.required' => 'Tanggal kunjungan wajib diisi.',
            'tanggal_kunjungan.after_or_equal' => 'Tanggal kunjungan harus hari ini atau sesudahnya.',
            'keluhan.required' => 'Keluhan wajib diisi.',
        ]);

        $result = $this->simpanAntrean($request, $request->id);
        if ($result instanceof RedirectResponse) {
            return $result;
        }

        return redirect()->route('pendaftaran.data')->with('success', 'Pendaftaran berhasil disimpan.');
    }

    public function pendaftaran_hapus($id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);

        $terpakai = DB::table('pemeriksaan')->where('pendaftaran_id', $id)->exists();
        if ($terpakai) {
            return redirect()->route('pendaftaran.data')->with('error', 'Pendaftaran tidak dapat dihapus karena sudah ada pemeriksaan.');
        }

        Antrean::where('pendaftaran_id', $id)->delete();
        $pendaftaran->delete();

        return redirect()->route('pendaftaran.data')->with('success', 'Pendaftaran berhasil dihapus.');
    }

    public function pendaftaran_saya()
    {
        $pasienId = auth()->user()->pasien_id;
        $pendaftaran = Pendaftaran::with(['dokter', 'jadwal', 'antrean'])
            ->where('pasien_id', $pasienId)
            ->orderBy('tanggal_kunjungan', 'desc')
            ->paginate(10)->withQueryString();

        return view('pendaftaran.saya', compact('pendaftaran'));
    }

    public function pendaftaran_daftar()
    {
        $jadwalList = JadwalDokter::with('dokter')
            ->whereHas('dokter', fn ($q) => $q->where('status', 'aktif'))
            ->orderBy('hari')->orderBy('jam_mulai')->get();

        return view('pendaftaran.daftar', compact('jadwalList'));
    }

    public function pendaftaran_pasien_simpan(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_dokter,id',
            'tanggal_kunjungan' => 'required|date|after_or_equal:today',
            'keluhan' => 'required|string|max:1000',
        ], [
            'jadwal_id.required' => 'Jadwal dokter wajib dipilih.',
            'tanggal_kunjungan.required' => 'Tanggal kunjungan wajib diisi.',
            'tanggal_kunjungan.after_or_equal' => 'Tanggal kunjungan harus hari ini atau sesudahnya.',
            'keluhan.required' => 'Keluhan wajib diisi.',
        ]);

        if (! auth()->user()->pasien_id) {
            return back()->with('error', 'Akun Anda belum terhubung ke data pasien. Hubungi admin.');
        }

        $request->merge(['pasien_id' => auth()->user()->pasien_id]);

        $result = $this->simpanAntrean($request, null);
        if ($result instanceof RedirectResponse) {
            return $result;
        }

        return redirect()->route('pendaftaran.saya')->with('success', 'Pendaftaran berhasil disimpan. Silakan menunggu nomor antrean.');
    }

    protected function simpanAntrean(Request $request, $id)
    {
        $jadwal = JadwalDokter::findOrFail($request->jadwal_id);
        $tanggalKunjungan = $request->tanggal_kunjungan;

        $mapHari = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 0];
        $hariPilihan = $mapHari[$jadwal->hari] ?? null;
        $hariTanggal = Carbon::parse($tanggalKunjungan)->dayOfWeek;
        $namaHariTanggal = array_search($hariTanggal, $mapHari) ?: 'Minggu';

        if ($hariPilihan !== $hariTanggal) {
            return back()->withErrors(['jadwal_id' => "Jadwal ini untuk hari {$jadwal->hari}, tetapi tanggal yang dipilih adalah {$namaHariTanggal}."])->withInput();
        }

        // Saat mengubah pendaftaran, pendaftaran itu sendiri jangan ikut dihitung.
        $kuotaTerpakai = Pendaftaran::where('jadwal_id', $jadwal->id)
            ->where('tanggal_kunjungan', $tanggalKunjungan)
            ->where('status', '!=', 'batal')
            ->when($id, fn ($q) => $q->where('id', '!=', $id))
            ->count();

        if ($kuotaTerpakai >= $jadwal->kuota) {
            return back()->withErrors(['jadwal_id' => 'Kuota jadwal ini sudah penuh untuk tanggal tersebut.'])->withInput();
        }

        $nomorPendaftaran = Pendaftaran::generateNomorPendaftaran();
        $jadwalId = $jadwal->id;

        return DB::transaction(function () use ($request, $id, $jadwalId, $tanggalKunjungan, $nomorPendaftaran) {
            // Kunci baris pendaftaran pada jadwal + tanggal ini supaya nomor antrean
            // tidak bentrok ketika ada pendaftaran bersamaan.
            Pendaftaran::where('jadwal_id', $jadwalId)
                ->where('tanggal_kunjungan', $tanggalKunjungan)
                ->lockForUpdate()
                ->get();

            $pendaftaran = Pendaftaran::updateOrCreate(
                ['id' => $id],
                [
                    'nomor_pendaftaran' => $nomorPendaftaran,
                    'pasien_id' => $request->pasien_id,
                    'dokter_id' => JadwalDokter::findOrFail($jadwalId)->dokter_id,
                    'jadwal_id' => $jadwalId,
                    'tanggal_kunjungan' => $tanggalKunjungan,
                    'keluhan' => $request->keluhan,
                    'status' => 'terdaftar',
                ]
            );

            // Pakai max, bukan count, supaya nomor yang sudah terlayani tidak dipakai
            // ulang ketika ada antrean yang dihapus.
            $nomorAntrean = (int) Antrean::where('tanggal', $tanggalKunjungan)
                ->whereHas('pendaftaran', fn ($q) => $q->where('jadwal_id', $jadwalId))
                ->when($id, fn ($q) => $q->where('pendaftaran_id', '!=', $id))
                ->max('nomor_antrean') + 1;

            if ($id) {
                Antrean::where('pendaftaran_id', $id)->update([
                    'nomor_antrean' => $nomorAntrean,
                    'tanggal' => $tanggalKunjungan,
                    'status' => 'menunggu',
                ]);
            } else {
                Antrean::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'nomor_antrean' => $nomorAntrean,
                    'tanggal' => $tanggalKunjungan,
                    'status' => 'menunggu',
                ]);
            }

            return $pendaftaran;
        });
    }
}
