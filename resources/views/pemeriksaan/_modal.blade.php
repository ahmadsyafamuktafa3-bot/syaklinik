<div class="modal fade" id="modalPemeriksaan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" action="{{ route('pemeriksaan.simpan') }}">
            @csrf
            <input type="hidden" name="id" id="prxid">
            <input type="hidden" name="pendaftaran_id" id="prxpendaftaran">
            <div class="modal-header"><h5 class="modal-title" id="modalTitle">Pemeriksaan Pasien</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="alert alert-info border-0 shadow-sm" id="prxInfo">
                    <strong>Pasien:</strong> <span id="prxNama">-</span> |
                    <strong>No. Pendaftaran:</strong> <span id="prxNoPd">-</span> |
                    <strong>No. Antrean:</strong> <span id="prxNoAntrean">-</span>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Pemeriksaan</label>
                        <input type="date" name="tanggal_pemeriksaan" id="prxtanggal" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Keluhan</label>
                        <textarea name="keluhan" id="prxkeluhan" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Pemeriksaan Fisik</label>
                        <textarea name="pemeriksaan_fisik" id="prxfisik" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Diagnosis</label>
                        <textarea name="diagnosis" id="prxdiagnosis" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Tindakan</label>
                        <div class="row">
                            @forelse ($tindakanList as $tindakan)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check border rounded-3 px-3 py-2">
                                        <input class="form-check-input prx-tindakan" type="checkbox" name="tindakan_id[]" value="{{ $tindakan->id }}">
                                        <label class="form-check-label">
                                            {{ $tindakan->nama_tindakan }}
                                            <span class="text-secondary">- Rp {{ number_format($tindakan->tarif, 0, ',', '.') }}</span>
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted">Belum ada data tindakan. Tambah dulu di menu Data Tindakan.</div>
                            @endforelse
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" id="prxcatatan" rows="2" class="form-control"></textarea>
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
@push('scripts')
<script>
function bukaPemeriksaan(data, title) {
    document.getElementById('prxid').value = data.id || '';
    document.getElementById('prxpendaftaran').value = data.pendaftaran || '';
    document.getElementById('prxNama').textContent = data.nama || '-';
    document.getElementById('prxNoPd').textContent = data.no_pd || '-';
    document.getElementById('prxNoAntrean').textContent = data.no_antrean || '-';
    document.getElementById('prxtanggal').value = data.tanggal || '';
    document.getElementById('prxkeluhan').value = data.keluhan || '';
    document.getElementById('prxfisik').value = data.fisik || '';
    document.getElementById('prxdiagnosis').value = data.diagnosis || '';
    document.getElementById('prxcatatan').value = data.catatan || '';
    document.querySelectorAll('.prx-tindakan').forEach(function (cb) {
        cb.checked = (data.tindakan && data.tindakan.indexOf(String(cb.value)) !== -1);
    });
    document.getElementById('modalTitle').textContent = title;
}
document.querySelectorAll('.btn-prx-add').forEach(btn => {
    btn.addEventListener('click', function () {
        bukaPemeriksaan({
            id: '', pendaftaran: this.dataset.pendaftaran, nama: this.dataset.nama,
            no_pd: this.dataset.no_pd, no_antrean: this.dataset.no_antrean,
            keluhan: this.dataset.keluhan, tanggal: this.dataset.tanggal || '',
            tindakan: '',
        }, 'Pemeriksaan Pasien');
    });
});
document.querySelectorAll('.btn-prx-edit').forEach(btn => {
    btn.addEventListener('click', function () {
        bukaPemeriksaan({
            id: this.dataset.id, pendaftaran: this.dataset.pendaftaran, nama: this.dataset.nama,
            no_pd: this.dataset.no_pd, no_antrean: this.dataset.no_antrean,
            tanggal: this.dataset.tanggal, keluhan: this.dataset.keluhan,
            fisik: this.dataset.fisik, diagnosis: this.dataset.diagnosis,
            catatan: this.dataset.catatan, tindakan: (this.dataset.tindakan || '').split(','),
        }, 'Edit Pemeriksaan');
    });
});
</script>
@endpush