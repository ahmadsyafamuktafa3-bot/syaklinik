@extends('layouts.app')
@section('title', 'Data Obat')
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show text-white" role="alert">
            @foreach ($errors->all() as $msg)
                <div><i class="bi bi-exclamation-triangle"></i> {{ $msg }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Data Obat</h4>
        <div>
            <a href="{{ route('obat.riwayat_stok') }}" class="btn btn-outline-secondary"><i class="bi bi-clock-history"></i> Riwayat Stok</a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalObat"><i class="bi bi-plus-circle"></i> Tambah Obat</button>
        </div>
    </div>
    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label mb-1">Pencarian</label>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kode/nama obat">
        </div>
        <div class="col-auto">
            <label class="form-label mb-1">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="tersedia" @selected(request('status') === 'tersedia')>Tersedia</option>
                <option value="habis" @selected(request('status') === 'habis')>Habis</option>
                <option value="kedaluwarsa" @selected(request('status') === 'kedaluwarsa')>Kedaluwarsa</option>
            </select>
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-dark text-white mb-0">Cari</button>
            <a href="{{ route('obat.data') }}" class="btn btn-secondary mb-0">Reset</a>
        </div>
    </form>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr><th>No</th><th>Kode</th><th>Nama Obat</th><th>Jenis</th><th>Satuan</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($obat as $i => $o)
                        <tr>
                            <td>{{ $obat->firstItem() + $i }}</td>
                            <td>{{ $o->kode_obat }}</td>
                            <td>{{ $o->nama_obat }}</td>
                            <td>{{ $o->jenis_obat }}</td>
                            <td>{{ $o->satuan }}</td>
                            <td>Rp {{ number_format($o->harga, 0, ',', '.') }}</td>
                            <td>{{ $o->stok }}</td>
                            <td><span class="badge bg-{{ $o->status === 'tersedia' ? 'success' : ($o->status === 'habis' ? 'danger' : 'warning text-dark') }}">{{ ucfirst($o->status) }}</span></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-edit"
                                    data-id="{{ $o->id }}"
                                    data-kode="{{ $o->kode_obat }}"
                                    data-nama="{{ $o->nama_obat }}"
                                    data-jenis="{{ $o->jenis_obat }}"
                                    data-satuan="{{ $o->satuan }}"
                                    data-harga="{{ $o->harga }}"
                                    data-tgl="{{ $o->tanggal_kedaluwarsa }}"
                                    data-status="{{ $o->status }}"
                                    data-stok="{{ $o->stok }}"
                                    data-bs-toggle="modal" data-bs-target="#modalObat">Edit</button>
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalMasuk{{ $o->id }}">Stok Masuk</button>
                                <button type="button" class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalKeluar{{ $o->id }}">Stok Keluar</button>
                                <form action="{{ route('obat.hapus', $o->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted">Belum ada data obat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $obat->links() }}</div>

    <div class="modal fade" id="modalObat" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('obat.simpan') }}">
                @csrf
                <input type="hidden" name="id" id="oid">
                <div class="modal-header"><h5 class="modal-title" id="modalTitle">Tambah Obat</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Obat</label>
                            <input type="text" name="kode_obat" id="okode" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Obat</label>
                            <input type="text" name="nama_obat" id="onama" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Obat</label>
                            <input type="text" name="jenis_obat" id="ojeniss" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" id="osatuan" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="harga" id="oharga" step="0.01" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Saat Ini</label>
                            <div class="form-control bg-gray-100" id="ostok">0</div>
                            <small class="text-muted">Ubah stok lewat menu Stok Masuk / Stok Keluar setelah obat tersimpan.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Kedaluwarsa</label>
                            <input type="date" name="tanggal_kedaluwarsa" id="otgl" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="ostatus" class="form-select">
                                <option value="tersedia">Tersedia</option>
                                <option value="habis">Habis</option>
                                <option value="kedaluwarsa">Kedaluwarsa</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($obat as $o)
        <div class="modal fade" id="modalMasuk{{ $o->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('obat.stok_masuk', $o->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Stok Masuk — {{ $o->nama_obat }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-sm text-secondary mb-2">Stok saat ini: <strong>{{ $o->stok }} {{ $o->satuan }}</strong></p>
                            <input type="number" name="jumlah" min="1" class="form-control mb-2" placeholder="Jumlah masuk">
                            <input type="text" name="keterangan" class="form-control" placeholder="Keterangan (cth. pembelian supplier)">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Simpan Masuk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalKeluar{{ $o->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('obat.stok_keluar', $o->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Stok Keluar — {{ $o->nama_obat }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-sm text-secondary mb-2">Stok saat ini: <strong>{{ $o->stok }} {{ $o->satuan }}</strong></p>
                            <input type="number" name="jumlah" min="1" class="form-control mb-2" placeholder="Jumlah keluar">
                            <input type="text" name="keterangan" class="form-control" placeholder="Keterangan (cth. rusak, kedaluwarsa, terpakai)">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning text-white">Simpan Keluar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
@push('scripts')
<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('oid').value = this.dataset.id;
        document.getElementById('okode').value = this.dataset.kode;
        document.getElementById('onama').value = this.dataset.nama;
        document.getElementById('ojeniss').value = this.dataset.jenis;
        document.getElementById('osatuan').value = this.dataset.satuan;
        document.getElementById('oharga').value = this.dataset.harga;
        document.getElementById('otgl').value = this.dataset.tgl;
        document.getElementById('ostatus').value = this.dataset.status;
        document.getElementById('ostok').textContent = this.dataset.stok + ' ' + this.dataset.satuan;
        document.getElementById('modalTitle').textContent = 'Edit Obat';
    });
});
var modalObatEl = document.getElementById('modalObat');
modalObatEl.addEventListener('show.bs.modal', function (e) {
    if (e.relatedTarget && !e.relatedTarget.classList.contains('btn-edit')) {
        document.getElementById('oid').value = '';
        document.getElementById('okode').value = '';
        document.getElementById('onama').value = '';
        document.getElementById('ojeniss').value = '';
        document.getElementById('osatuan').value = '';
        document.getElementById('oharga').value = '';
        document.getElementById('otgl').value = '';
        document.getElementById('ostatus').value = 'tersedia';
        document.getElementById('ostok').textContent = '0';
        document.getElementById('modalTitle').textContent = 'Tambah Obat';
    }
});
</script>
@endpush