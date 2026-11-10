@extends('layouts.app')
@section('title', 'Data Tindakan')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Data Tindakan</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTindakan"><i class="bi bi-plus-circle"></i> Tambah Tindakan</button>
    </div>
    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label mb-1">Pencarian</label>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama tindakan">
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-dark text-white mb-0">Cari</button>
            <a href="{{ route('tindakan.data') }}" class="btn btn-secondary mb-0">Reset</a>
        </div>
    </form>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr><th>No</th><th>Nama Tindakan</th><th>Tarif</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($tindakan as $i => $t)
                        <tr>
                            <td>{{ $tindakan->firstItem() + $i }}</td>
                            <td>{{ $t->nama_tindakan }}</td>
                            <td>Rp {{ number_format($t->tarif, 0, ',', '.') }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit" data-id="{{ $t->id }}" data-nama="{{ $t->nama_tindakan }}" data-tarif="{{ $t->tarif }}" data-bs-toggle="modal" data-bs-target="#modalTindakan">Edit</button>
                                <form action="{{ route('tindakan.hapus', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">Belum ada data tindakan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $tindakan->links() }}</div>

    <div class="modal fade" id="modalTindakan" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('tindakan.simpan') }}">
                @csrf
                <input type="hidden" name="id" id="tid">
                <div class="modal-header"><h5 class="modal-title" id="modalTitle">Tambah Tindakan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Tindakan</label>
                        <input type="text" name="nama_tindakan" id="tnama" class="form-control @error('nama_tindakan') is-invalid @enderror" value="{{ old('nama_tindakan') }}">
                        @error('nama_tindakan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tarif (Rp)</label>
                        <input type="number" name="tarif" id="ttarif" class="form-control @error('tarif') is-invalid @enderror" value="{{ old('tarif') }}">
                        @error('tarif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
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
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('tid').value = this.dataset.id;
        document.getElementById('tnama').value = this.dataset.nama;
        document.getElementById('ttarif').value = this.dataset.tarif;
        document.getElementById('modalTitle').textContent = 'Edit Tindakan';
    });
});
</script>
@endpush