@extends('layouts.app')
@section('title', 'Data Pasien')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Data Pasien</h4>
        <div>
            <a href="{{ route('pasien.cetak', ['q' => request('q')]) }}" target="_blank" class="btn btn-warning">Cetak</a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPasien">
                <i class="bi bi-plus-circle"></i> Tambah Pasien
            </button>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label mb-1">Pencarian</label>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama, NIK, atau No. Rekam Medis">
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-dark text-white mb-0">Cari</button>
            <a href="{{ route('pasien.data') }}" class="btn btn-secondary mb-0">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>No. Rekam Medis</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>JK</th>
                        <th>No. Telepon</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pasien as $i => $p)
                        <tr>
                            <td>{{ $pasien->firstItem() + $i }}</td>
                            <td>{{ $p->nomor_rekam_medis }}</td>
                            <td>{{ $p->nik }}</td>
                            <td>{{ $p->nama_pasien }}</td>
                            <td>{{ $p->jenis_kelamin }}</td>
                            <td>{{ $p->nomor_telepon }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-edit"
                                    data-id="{{ $p->id }}"
                                    data-rm="{{ $p->nomor_rekam_medis }}"
                                    data-nik="{{ $p->nik }}"
                                    data-nama="{{ $p->nama_pasien }}"
                                    data-jk="{{ $p->jenis_kelamin }}"
                                    data-tgl="{{ $p->tanggal_lahir }}"
                                    data-telp="{{ $p->nomor_telepon }}"
                                    data-email="{{ $p->email }}"
                                    data-alamat="{{ $p->alamat }}"
                                    data-username="{{ $usernames[$p->id] ?? '' }}"
                                    data-bs-toggle="modal" data-bs-target="#modalPasien">Edit</button>
                                <form action="{{ route('pasien.hapus', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus pasien ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Belum ada data pasien.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $pasien->links() }}</div>

    <div class="modal fade" id="modalPasien" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('pasien.simpan') }}">
                @csrf
                <input type="hidden" name="id" id="pid">
                <div class="modal-header"><h5 class="modal-title" id="modalTitle">Tambah Pasien</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Rekam Medis</label>
                            <input type="text" name="nomor_rekam_medis" id="prm" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" id="pnik" maxlength="16" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pasien</label>
                            <input type="text" name="nama_pasien" id="pnama" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="pjk" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="ptgl" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" id="ptelp" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email (opsional)</label>
                            <input type="email" name="email" id="pemail" class="form-control">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" id="palamat" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <hr>
                            <h6 class="fw-bold">Akun Login Pasien <span class="text-secondary small">(opsional)</span></h6>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" id="pusername" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" id="ppassword" class="form-control" placeholder="Minimal 6 karakter">
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
function isiPasien(data, title) {
    document.getElementById('pid').value = data.id || '';
    document.getElementById('prm').value = data.rm || '';
    document.getElementById('pnik').value = data.nik || '';
    document.getElementById('pnama').value = data.nama || '';
    document.getElementById('pjk').value = data.jk || '';
    document.getElementById('ptgl').value = data.tgl || '';
    document.getElementById('ptelp').value = data.telp || '';
    document.getElementById('pemail').value = data.email || '';
    document.getElementById('palamat').value = data.alamat || '';
    document.getElementById('pusername').value = data.username || '';
    document.getElementById('ppassword').value = '';
    document.getElementById('ppassword').placeholder = data.id ? 'Kosongkan jika tidak diganti' : 'Minimal 6 karakter';
    document.getElementById('modalTitle').textContent = title;
}
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {
        isiPasien({
            id: this.dataset.id, rm: this.dataset.rm, nik: this.dataset.nik,
            nama: this.dataset.nama, jk: this.dataset.jk, tgl: this.dataset.tgl,
            telp: this.dataset.telp, email: this.dataset.email, alamat: this.dataset.alamat,
            username: this.dataset.username,
        }, 'Edit Pasien');
    });
});
var modalPasienEl = document.getElementById('modalPasien');
modalPasienEl.addEventListener('show.bs.modal', function (e) {
    if (e.relatedTarget && !e.relatedTarget.classList.contains('btn-edit')) {
        isiPasien({}, 'Tambah Pasien');
    }
});
</script>
@endpush