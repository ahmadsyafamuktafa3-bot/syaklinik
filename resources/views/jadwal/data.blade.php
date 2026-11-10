@extends('layouts.app')
@section('title', 'Jadwal Dokter')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Jadwal Dokter</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalJadwal"><i class="bi bi-plus-circle"></i> Tambah Jadwal</button>
    </div>
    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label mb-1">Pencarian</label>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/kode dokter atau ruang">
        </div>
        <div class="col-auto">
            <label class="form-label mb-1">Hari</label>
            <select name="hari" class="form-select">
                <option value="">Semua Hari</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                    <option value="{{ $h }}" @selected(request('hari') === $h)>{{ $h }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-dark text-white mb-0">Cari</button>
            <a href="{{ route('jadwal.data') }}" class="btn btn-secondary mb-0">Reset</a>
        </div>
    </form>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr><th>No</th><th>Dokter</th><th>Hari</th><th>Jam</th><th>Ruang</th><th>Kuota</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($jadwal as $i => $j)
                        <tr>
                            <td>{{ $jadwal->firstItem() + $i }}</td>
                            <td>{{ $j->dokter->nama_dokter ?? '-' }}</td>
                            <td><span class="badge bg-primary">{{ $j->hari }}</span></td>
                            <td>{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</td>
                            <td>{{ $j->ruang }}</td>
                            <td>{{ $j->kuota }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-edit"
                                    data-id="{{ $j->id }}"
                                    data-dokter="{{ $j->dokter_id }}"
                                    data-hari="{{ $j->hari }}"
                                    data-mulai="{{ substr($j->jam_mulai, 0, 5) }}"
                                    data-selesai="{{ substr($j->jam_selesai, 0, 5) }}"
                                    data-ruang="{{ $j->ruang }}"
                                    data-kuota="{{ $j->kuota }}"
                                    data-bs-toggle="modal" data-bs-target="#modalJadwal">Edit</button>
                                <form action="{{ route('jadwal.hapus', $j->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Belum ada data jadwal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $jadwal->links() }}</div>

    <div class="modal fade" id="modalJadwal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('jadwal.simpan') }}">
                @csrf
                <input type="hidden" name="id" id="jid">
                <div class="modal-header"><h5 class="modal-title" id="modalTitle">Tambah Jadwal</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dokter</label>
                            <select name="dokter_id" id="jdokter" class="form-select">
                                <option value="">-- Pilih Dokter --</option>
                                @foreach ($dokterList as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_dokter }} ({{ $d->spesialisasi }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hari <span class="text-secondary small toggle-add" id="hariInfoAdd">(bisa pilih beberapa)</span></label>
                            <div id="hariCheckbox" class="d-flex flex-wrap gap-2">
                                @foreach ($hariList as $h)
                                    <div class="form-check form-check-inline border rounded-3 px-3 py-1">
                                        <input class="form-check-input" type="checkbox" name="hari[]" value="{{ $h }}" id="hari-{{ $loop->index }}">
                                        <label class="form-check-label" for="hari-{{ $loop->index }}">{{ $h }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div id="hariEdit" class="d-none"></div>
                            <input type="hidden" id="jhari" name="hari" value="" disabled>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" id="jmulai" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" id="jselesai" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ruang</label>
                            <input type="text" name="ruang" id="jruang" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kuota Pasien</label>
                            <input type="number" name="kuota" id="jkuota" min="1" class="form-control">
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
function modeJadwal(add) {
    var cbs = document.querySelectorAll('#hariCheckbox input[type="checkbox"]');
    var hid = document.getElementById('jhari');
    var editBox = document.getElementById('hariEdit');
    if (add) {
        cbs.forEach(function (cb) { cb.disabled = false; cb.checked = false; });
        hid.disabled = true; hid.value = '';
        editBox.classList.add('d-none');
        document.getElementById('hariInfoAdd').classList.remove('d-none');
    } else {
        cbs.forEach(function (cb) { cb.disabled = true; cb.checked = false; });
        hid.disabled = false;
        editBox.classList.remove('d-none');
        document.getElementById('hariInfoAdd').classList.add('d-none');
    }
}
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('jid').value = this.dataset.id;
        document.getElementById('jdokter').value = this.dataset.dokter;
        document.getElementById('jmulai').value = this.dataset.mulai;
        document.getElementById('jselesai').value = this.dataset.selesai;
        document.getElementById('jruang').value = this.dataset.ruang;
        document.getElementById('jkuota').value = this.dataset.kuota;
        document.getElementById('jhari').value = this.dataset.hari;
        document.getElementById('hariEdit').innerHTML =
            '<div class="form-control bg-gray-100">' + this.dataset.hari + '</div>' +
            '<small class="text-muted">Hari tidak bisa diubah saat edit. Hapus lalu buat ulang jika ingin pindah hari.</small>';
        modeJadwal(false);
        document.getElementById('modalTitle').textContent = 'Edit Jadwal';
    });
});
var modalJadwalEl = document.getElementById('modalJadwal');
modalJadwalEl.addEventListener('show.bs.modal', function (e) {
    if (e.relatedTarget && !e.relatedTarget.classList.contains('btn-edit')) {
        document.getElementById('jid').value = '';
        document.getElementById('jdokter').value = '';
        document.getElementById('jmulai').value = '';
        document.getElementById('jselesai').value = '';
        document.getElementById('jruang').value = '';
        document.getElementById('jkuota').value = '';
        modeJadwal(true);
        document.getElementById('modalTitle').textContent = 'Tambah Jadwal';
    }
});
</script>
@endpush