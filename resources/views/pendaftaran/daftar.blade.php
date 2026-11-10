@extends('layouts.app')
@section('title', 'Daftar Periksa')
@section('content')
    <h4 class="fw-bold mb-3">Pendaftaran Periksa</h4>
    <div class="card shadow-sm">
        <div class="card-body">
            <p class="text-sm text-secondary mb-3">Pilih jadwal dokter sesuai hari kunjungan, lalu isi keluhan Anda.</p>
            <form method="POST" action="{{ route('pendaftaran.pasien_simpan') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jadwal Dokter</label>
                        <select name="jadwal_id" class="form-select @error('jadwal_id') is-invalid @enderror">
                            <option value="">-- Pilih Jadwal --</option>
                            @foreach ($jadwalList as $jadwal)
                                <option value="{{ $jadwal->id }}" @selected(old('jadwal_id') == $jadwal->id)>
                                    {{ $jadwal->dokter->nama_dokter ?? '-' }} | {{ $jadwal->hari }} {{ substr($jadwal->jam_mulai, 0, 5) }}-{{ substr($jadwal->jam_selesai, 0, 5) }} | Ruang {{ $jadwal->ruang }}
                                </option>
                            @endforeach
                        </select>
                        @error('jadwal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Kunjungan</label>
                        <input type="date" name="tanggal_kunjungan" class="form-control @error('tanggal_kunjungan') is-invalid @enderror" value="{{ old('tanggal_kunjungan') }}" min="{{ date('Y-m-d') }}">
                        @error('tanggal_kunjungan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Keluhan</label>
                        <textarea name="keluhan" class="form-control @error('keluhan') is-invalid @enderror" rows="3">{{ old('keluhan') }}</textarea>
                        @error('keluhan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('pendaftaran.saya') }}" class="btn btn-secondary mb-0">Batal</a>
                    <button type="submit" class="btn btn-primary mb-0">Daftar</button>
                </div>
            </form>
        </div>
    </div>
@endsection