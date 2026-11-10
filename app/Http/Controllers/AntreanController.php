<?php

namespace App\Http\Controllers;

use App\Models\Antrean;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AntreanController extends Controller
{
    public function antrean_data(Request $request)
    {
        $tanggal = $request->get('tanggal') ?: null;
        $status = $request->get('status') ?: null;
        $dokterId = $request->get('dokter_id') ?: null;

        $antrean = Antrean::with(['pendaftaran.pasien', 'pendaftaran.dokter', 'pendaftaran.jadwal'])
            ->when($tanggal, fn ($q, $tanggal) => $q->where('tanggal', $tanggal))
            ->when($status, fn ($q, $status) => $q->where('status', $status))
            ->when($dokterId, fn ($q, $dokterId) => $q->whereHas('pendaftaran', fn ($p) => $p->where('dokter_id', $dokterId)))
            ->orderByDesc('tanggal')
            // Nomor antrean dimulai dari 1 per dokter, jadi dikelompokkan per dokter
            // dulu supaya tidak terlihat seakan-akan nomornya bentrok.
            ->orderByRaw('(SELECT dokter_id FROM pendaftaran WHERE pendaftaran.id = antrean.pendaftaran_id) asc')
            ->orderBy('nomor_antrean')
            ->paginate(15)->withQueryString();

        $dokterList = Dokter::orderBy('nama_dokter')->get();
        $tanggalList = Antrean::query()->distinct()->orderByDesc('tanggal')->limit(120)->pluck('tanggal');

        return view('antrean.data', compact('antrean', 'dokterList', 'tanggalList', 'tanggal', 'status', 'dokterId'));
    }

    public function antrean_status(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,dipanggil,diperiksa,selesai,batal',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        $antrean = Antrean::with('pendaftaran')->findOrFail($id);

        DB::transaction(function () use ($antrean, $request) {
            $antrean->update(['status' => $request->status]);

            // Hanya pembatalan yang dicerminkan ke pendaftaran. Status lain tidak boleh
            // membatalkan atau menghidupkan kembali pendaftaran yang statusnya sudah final.
            if ($request->status === 'batal' && $antrean->pendaftaran?->status !== 'batal') {
                $antrean->pendaftaran?->update(['status' => 'batal']);
            }
        });

        return redirect()->to($this->kembaliKeDaftar())->with('success', "Status antrean No. {$antrean->nomor_antrean} diubah menjadi ".ucfirst($request->status).'.');
    }

    public function antrean_hapus($id)
    {
        $antrean = Antrean::with('pendaftaran')->findOrFail($id);

        DB::transaction(function () use ($antrean) {
            if ($antrean->pendaftaran?->status !== 'batal') {
                $antrean->pendaftaran?->update(['status' => 'batal']);
            }

            $antrean->delete();
        });

        return redirect()->to($this->kembaliKeDaftar())->with('success', 'Antrean berhasil dibatalkan/dihapus.');
    }

    private function kembaliKeDaftar(): string
    {
        $sebelumnya = url()->previous();
        $asal = url('/');

        // Hanya terima URL pada origin yang sama supaya tidak bisa jadi open redirect.
        if ($sebelumnya !== url()->current() && str_starts_with($sebelumnya, $asal)) {
            return $sebelumnya;
        }

        return route('antrean.data');
    }
}
