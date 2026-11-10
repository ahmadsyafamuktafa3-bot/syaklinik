<?php

namespace App\Http\Controllers;

use App\Models\Tindakan;
use Illuminate\Http\Request;

class TindakanController extends Controller
{
    public function tindakan_data(Request $request)
    {
        $keyword = $request->get('q');

        $tindakan = Tindakan::query()
            ->when($keyword, function ($query, $keyword) {
                $query->where('nama_tindakan', 'like', "%$keyword%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)->withQueryString();

        return view('tindakan.data', compact('tindakan'));
    }

    public function tindakan_simpan(Request $request)
    {
        $request->validate([
            'nama_tindakan' => 'required|string|max:255',
            'tarif' => 'required|numeric|min:0',
        ], [
            'nama_tindakan.required' => 'Nama tindakan wajib diisi.',
            'tarif.required' => 'Tarif wajib diisi.',
            'tarif.numeric' => 'Tarif harus berupa angka.',
            'tarif.min' => 'Tarif tidak boleh negatif.',
        ]);

        Tindakan::updateOrCreate(
            ['id' => $request->id],
            $request->only(['nama_tindakan', 'tarif'])
        );

        return back()->with('success', 'Data tindakan berhasil disimpan.');
    }

    public function tindakan_hapus($id)
    {
        Tindakan::findOrFail($id)->delete();
        return back()->with('success', 'Data tindakan berhasil dihapus.');
    }
}