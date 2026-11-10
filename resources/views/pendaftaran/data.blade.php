@extends('layouts.app')
@section('title', 'Data Pendaftaran')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h4 class="fw-bold mb-0">Data Pendaftaran</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPendaftaran">
            <i class="bi bi-plus-circle"></i> Tambah Pendaftaran
        </button>
    </div>

    <form method="GET" class="row g-2 mb-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label mb-1">Pencarian</label>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari no. pendaftaran, pasien, atau dokter">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label mb-1">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="terdaftar" @selected(request('status') === 'terdaftar')>Terdaftar</option>
                <option value="batal" @selected(request('status') === 'batal')>Batal</option>
            </select>
        </div>
        <div class="col-auto d-flex flex-wrap align-items-center gap-2">
            <button class="btn btn-dark text-white mb-0">Cari</button>
            <a href="{{ route('pendaftaran.data') }}" class="btn btn-secondary mb-0">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>No. Pendaftaran</th>
                        <th>Tanggal</th>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Jadwal</th>
                        <th>Antrean</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendaftaran as $i => $p)
                        <tr>
                            <td>{{ $pendaftaran->firstItem() + $i }}</td>
                            <td>{{ $p->nomor_pendaftaran }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_kunjungan)->format('d/m/Y') }}</td>
                            <td>{{ $p->pasien->nama_pasien ?? '-' }}</td>
                            <td>{{ $p->dokter->nama_dokter ?? '-' }}</td>
                            <td>
                                @if ($p->jadwal)
                                    {{ $p->jadwal->hari }} {{ substr($p->jadwal->jam_mulai, 0, 5) }}-{{ substr($p->jadwal->jam_selesai, 0, 5) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($p->antrean)
                                    <span class="badge bg-primary">{{ $p->antrean->nomor_antrean }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $p->status === 'batal' ? 'bg-danger' : 'bg-success' }}">{{ ucfirst($p->status) }}</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-edit"
                                    data-id="{{ $p->id }}"
                                    data-pasien="{{ $p->pasien_id }}"
                                    data-jadwal="{{ $p->jadwal_id }}"
                                    data-tanggal="{{ $p->tanggal_kunjungan }}"
                                    data-keluhan="{{ $p->keluhan }}"
                                    data-bs-toggle="modal" data-bs-target="#modalPendaftaran">Edit</button>
                                <form action="{{ route('pendaftaran.hapus', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus pendaftaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted">Belum ada data pendaftaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $pendaftaran->links() }}</div>

    <div class="modal fade" id="modalPendaftaran" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('pendaftaran.simpan') }}">
                @csrf
                <input type="hidden" name="id" id="pdid">
                <div class="modal-header"><h5 class="modal-title" id="modalTitle">Tambah Pendaftaran</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pasien</label>
                            <select name="pasien_id" id="pdpasien" class="form-select">
                                <option value="">-- Pilih Pasien --</option>
                                @foreach ($pasienList as $pasien)
                                    <option value="{{ $pasien->id }}">{{ $pasien->nama_pasien }} ({{ $pasien->nomor_rekam_medis }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jadwal Dokter</label>
                            <select name="jadwal_id" id="pdjadwal" class="form-select">
                                <option value="">-- Pilih Jadwal --</option>
                                @foreach ($jadwalList as $jadwal)
                                    <option value="{{ $jadwal->id }}">{{ $jadwal->dokter->nama_dokter ?? '-' }} | {{ $jadwal->hari }} {{ substr($jadwal->jam_mulai, 0, 5) }}-{{ substr($jadwal->jam_selesai, 0, 5) }} | Ruang {{ $jadwal->ruang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Kunjungan</label>
                            <input type="date" name="tanggal_kunjungan" id="pdtanggal" class="form-control" min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Keluhan</label>
                            <textarea name="keluhan" id="pdkeluhan" class="form-control" rows="3"></textarea>
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
function isiPendaftaran(data, title) {
    document.getElementById('pdid').value = data.id || '';
    document.getElementById('pdpasien').value = data.pasien || '';
    document.getElementById('pdjadwal').value = data.jadwal || '';
    document.getElementById('pdtanggal').value = data.tanggal || '';
    document.getElementById('pdkeluhan').value = data.keluhan || '';
    document.getElementById('modalTitle').textContent = title;
}
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {
        isiPendaftaran({
            id: this.dataset.id, pasien: this.dataset.pasien, jadwal: this.dataset.jadwal,
            tanggal: this.dataset.tanggal, keluhan: this.dataset.keluhan,
        }, 'Edit Pendaftaran');
    });
});
var modalPdEl = document.getElementById('modalPendaftaran');
modalPdEl.addEventListener('show.bs.modal', function (e) {
    if (e.relatedTarget && !e.relatedTarget.classList.contains('btn-edit')) {
        isiPendaftaran({}, 'Tambah Pendaftaran');
    }
});
</script>
@endpush