@extends('layouts.app')
@section('title', 'Data Dokter')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Data Dokter</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDokter"><i class="bi bi-plus-circle"></i> Tambah Dokter</button>
    </div>
    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label mb-1">Pencarian</label>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/kode/spesialisasi">
        </div>
        <div class="col-auto">
            <label class="form-label mb-1">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="tidak_aktif" @selected(request('status') === 'tidak_aktif')>Tidak Aktif</option>
            </select>
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-dark text-white mb-0">Cari</button>
            <a href="{{ route('dokter.data') }}" class="btn btn-secondary mb-0">Reset</a>
        </div>
    </form>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr><th>No</th><th>Kode</th><th>Nama Dokter</th><th>Spesialisasi</th><th>Telepon</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($dokter as $i => $d)
                        <tr>
                            <td>{{ $dokter->firstItem() + $i }}</td>
                            <td>{{ $d->kode_dokter }}</td>
                            <td>{{ $d->nama_dokter }}</td>
                            <td>{{ $d->spesialisasi }}</td>
                            <td>{{ $d->nomor_telepon }}</td>
                            <td>
                                <span class="badge bg-{{ $d->status === 'aktif' ? 'success' : 'secondary' }}">{{ $d->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-edit"
                                    data-id="{{ $d->id }}"
                                    data-kode="{{ $d->kode_dokter }}"
                                    data-nama="{{ $d->nama_dokter }}"
                                    data-spesialisasi="{{ $d->spesialisasi }}"
                                    data-telp="{{ $d->nomor_telepon }}"
                                    data-status="{{ $d->status }}"
                                    data-alamat="{{ $d->alamat }}"
                                    data-username="{{ $usernames[$d->id] ?? '' }}"
                                    data-bs-toggle="modal" data-bs-target="#modalDokter">Edit</button>
                                <form action="{{ route('dokter.hapus', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Belum ada data dokter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $dokter->links() }}</div>

    <div class="modal fade" id="modalDokter" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('dokter.simpan') }}">
                @csrf
                <input type="hidden" name="id" id="did">
                <div class="modal-header"><h5 class="modal-title" id="modalTitle">Tambah Dokter</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Dokter</label>
                            <input type="text" name="kode_dokter" id="dkode" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Dokter</label>
                            <input type="text" name="nama_dokter" id="dnama" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Spesialisasi</label>
                            <input type="text" name="spesialisasi" id="dspesialisasi" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" id="dtelp" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="dstatus" class="form-select">
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" id="dalamat" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <hr>
                            <h6 class="fw-bold">Akun Login Dokter <span class="text-secondary small">(opsional)</span></h6>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" id="dusername" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" id="dpassword" class="form-control" placeholder="Minimal 6 karakter">
                                </div>
                            </div>
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
@endsection
@push('scripts')
<script>
function isiDokter(data, title) {
    document.getElementById('did').value = data.id || '';
    document.getElementById('dkode').value = data.kode || '';
    document.getElementById('dnama').value = data.nama || '';
    document.getElementById('dspesialisasi').value = data.spesialisasi || '';
    document.getElementById('dtelp').value = data.telp || '';
    document.getElementById('dstatus').value = data.status || 'aktif';
    document.getElementById('dalamat').value = data.alamat || '';
    document.getElementById('dusername').value = data.username || '';
    document.getElementById('dpassword').value = '';
    document.getElementById('dpassword').placeholder = data.id ? 'Kosongkan jika tidak diganti' : 'Minimal 6 karakter';
    document.getElementById('modalTitle').textContent = title;
}
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {
        isiDokter({
            id: this.dataset.id, kode: this.dataset.kode, nama: this.dataset.nama,
            spesialisasi: this.dataset.spesialisasi, telp: this.dataset.telp,
            status: this.dataset.status, alamat: this.dataset.alamat, username: this.dataset.username,
        }, 'Edit Dokter');
    });
});
var modalDokterEl = document.getElementById('modalDokter');
modalDokterEl.addEventListener('show.bs.modal', function (e) {
    if (e.relatedTarget && !e.relatedTarget.classList.contains('btn-edit')) {
        isiDokter({}, 'Tambah Dokter');
    }
});
</script>
@endpush