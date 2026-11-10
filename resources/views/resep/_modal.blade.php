<div class="modal fade" id="modalResep" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form class="modal-content" method="POST" action="{{ route('resep.simpan') }}">
            @csrf
            <input type="hidden" name="id" id="rsid">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Buat Resep</h5>
                    <span class="text-secondary small">Stok obat akan berkurang otomatis sesuai resep.</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="rsEditInfo" class="d-none">
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label">Pasien</label>
                            <input type="text" id="rsPasien" class="form-control" disabled>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Pemeriksaan</label>
                            <input type="text" id="rsTglPeriksa" class="form-control" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Resep</label>
                            <input type="date" name="tanggal" id="rsTglAdd" class="form-control" max="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div id="rsAddInfo">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Pemeriksaan Pasien</label>
                            <select name="pemeriksaan_id" id="rsPemeriksaan" class="form-select">
                                <option value="">-- Pilih Pemeriksaan yang Sudah Dilakukan --</option>
                                @foreach ($pemeriksaanList as $p)
                                    <option value="{{ $p->id }}" {{ $presetPemeriksaanId == $p->id ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::parse($p->tanggal_pemeriksaan)->format('d/m/Y') }} - {{ $p->pasien->nama_pasien ?? '-' }} ({{ $p->diagnosis ?: 'tanpa diagnosis' }})
                                    </option>
                                @endforeach
                            </select>
                            @if ($pemeriksaanList->isEmpty())
                                <small class="text-muted">Belum ada pemeriksaan. Isi pemeriksaan pasien terlebih dahulu.</small>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Resep</label>
                            <input type="date" name="tanggal" id="rsTglAdd2" class="form-control" max="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span>Daftar Obat</span>
                        <button type="button" class="btn btn-sm btn-light" id="rsTambahBaris">+ Tambah Obat</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35%">Obat</th>
                                        <th width="15%">Jumlah</th>
                                        <th width="25%">Aturan Pakai</th>
                                        <th width="15%">Subtotal</th>
                                        <th width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody id="rsBarisObat">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total</th>
                                        <th id="rsTotalHarga">Rp 0</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <label class="form-label">Catatan Resep</label>
                        <textarea name="catatan_resep" id="rsCatatan" class="form-control" rows="2" placeholder="cth. Apoteker harap menjelaskan aturan pakai ke pasien."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Resep</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        @php
            $obatJson = $obatList->map(fn($o) => ['id' => $o->id, 'nama' => $o->nama_obat, 'harga' => $o->harga, 'stok' => $o->stok])->values();
        @endphp
        const obatList = @json($obatJson);

        function formatRupiah(n) {
            return 'Rp ' + n.toLocaleString('id-ID');
        }

        function hitungRsBaris(row) {
            const harga = parseInt($(row).find('.rs-obat option:selected').data('harga')) || 0;
            const jumlah = parseInt($(row).find('.rs-jumlah').val()) || 0;
            const subtotal = harga * jumlah;
            $(row).find('.rs-subtotal').text(formatRupiah(subtotal));

            const stokValid = parseInt($(row).find('.rs-obat option:selected').data('stok')) || 0;
            if (jumlah > 0 && stokValid < jumlah) {
                $(row).find('.rs-obat').css('border', '2px solid #e91e63');
            } else {
                $(row).find('.rs-obat').css('border', '');
            }

            let total = 0;
            $('#rsBarisObat tr').each(function () {
                total += (parseInt($(this).find('.rs-obat option:selected').data('harga')) || 0) * (parseInt($(this).find('.rs-jumlah').val()) || 0);
            });
            $('#rsTotalHarga').text(formatRupiah(total));
        }

        function rsTambahBaris(data) {
            const opts = obatList.map(o =>
                `<option value="${o.id}" data-harga="${o.harga}" data-stok="${o.stok}" ${data && data.id === o.id ? 'selected' : ''}>${o.nama} (stok: ${o.stok})</option>`
            ).join('');

            const tr = $(`<tr>
                <td><select name="obat_id[]" class="form-select rs-obat">${opts}</select></td>
                <td><input type="number" name="jumlah[]" class="form-control rs-jumlah" min="1" value="1" required></td>
                <td><input type="text" name="aturan_pakai[]" class="form-control" value="${data && data.aturan ? data.aturan : ''}" placeholder="cth. 3 x sehari 1 tablet"></td>
                <td class="rs-subtotal">Rp 0</td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger rsHapusBaris">&times;</button></td>
            </tr>`);
            $('#rsBarisObat').append(tr);
            hitungRsBaris(tr);
        }

        function isiResep(data) {
            $('#rsid').val(data.id || '');
            $('#rsCatatan').val(data.catatan || '');

            if (data.id) {
                $('#rsEditInfo').removeClass('d-none');
                $('#rsAddInfo').addClass('d-none');
                $('#rsPasien').val(data.pasien || '-');
                $('#rsTglPeriksa').val(data.tgl_periksa || '-');
                $('#rsTglAdd').val(data.tanggal || '');
                $('#rsTglAdd').prop('name', 'tanggal');
                $('#rsTglAdd2').prop('name', '');
            } else {
                $('#rsEditInfo').addClass('d-none');
                $('#rsAddInfo').removeClass('d-none');
                $('#rsTglAdd').val('');
                $('#rsTglAdd').prop('name', '');
                $('#rsTglAdd2').prop('name', 'tanggal');
                $('#rsPemeriksaan').val(data.pemeriksaan || '');
                if (data.pemeriksaan) {
                    $('#rsPemeriksaan').val(String(data.pemeriksaan)).trigger('change');
                }
            }

            $('#rsBarisObat').empty();
            if (data.detail && data.detail.length) {
                data.detail.forEach(d => rsTambahBaris(d));
            } else {
                rsTambahBaris({});
            }
            $('#rsBarisObat tr').each(function () { hitungRsBaris($(this)); });
        }

        $(function () {
            $('#rsTambahBaris').on('click', function () { rsTambahBaris({}); });

            $(document).on('click', '.rsHapusBaris', function () {
                const tbody = $('#rsBarisObat');
                $(this).closest('tr').remove();
                if (!tbody.children().length) rsTambahBaris({});
                tbody.find('tr').each(function () { hitungRsBaris($(this)); });
            });

            $(document).on('change', '.rs-obat', function () { hitungRsBaris($(this).closest('tr')); });
            $(document).on('input', '.rs-jumlah', function () { hitungRsBaris($(this).closest('tr')); });
        });

        document.querySelectorAll('.btn-resep-tambah').forEach(btn => {
            btn.addEventListener('click', function () {
                var data = { id: '', pemeriksaan: this.dataset.pemeriksaan || '', tanggal: '', catatan: '', detail: [] };
                var selPemeriksaan = document.getElementById('rsPemeriksaan');
                if (data.pemeriksaan) {
                    if ($('#rsPemeriksaan option[value="' + data.pemeriksaan + '"]').length) {
                        $('#rsPemeriksaan').val(data.pemeriksaan);
                    }
                }
                isiResep(data);
                document.querySelector('#modalResep .modal-title').textContent = 'Buat Resep';
            });
        });
        document.querySelectorAll('.btn-resep-edit').forEach(btn => {
            btn.addEventListener('click', function () {
                var detail = [];
                try { detail = JSON.parse(this.dataset.detail || '[]'); } catch (e) { detail = []; }
                isiResep({
                    id: this.dataset.id, pasien: this.dataset.pasien,
                    tgl_periksa: this.dataset.tgl_periksa, tanggal: this.dataset.tanggal,
                    catatan: this.dataset.catatan, detail: detail,
                });
                document.querySelector('#modalResep .modal-title').textContent = 'Edit Resep';
            });
        });
    </script>
@endpush