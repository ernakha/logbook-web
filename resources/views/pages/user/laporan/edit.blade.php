@extends('layouts.master')
@section('content')
<style>
    .card-header-custom {
        background-color: #06923E;
        color: #fff;
        border: none;
    }

    .btn-simpan {
        background-color: #06923E;
        color: #fff;
        border: none;
    }

    .btn-simpan:hover {
        background-color: #2aa15a;
        color: #fff;
    }

    .btn-batal {
        border: 2px solid #06923E;
        color: #06923E;
        background-color: transparent;
    }

    .btn-batal:hover {
        background-color: #f8f9fa;
        color: #2aa15a;
        border-color: #2aa15a;
    }
</style>

<div class="row">
    <div class="col-lg-12 d-flex align-items-stretch">
        <div class="card w-100 shadow-sm">
            <div class="card-header card-header-custom">
                <h5 class="mb-0 text-white">Tambah Laporan Monitoring</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('laporan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Pegawai --}}
                    <input type="hidden" name="pegawai_id" value="{{ $laporan->pegawai->id }}">
                    <div class="mb-3">
                        <label class="form-label">Pegawai</label>
                        <input type="text" class="form-control" value="{{ $laporan->pegawai->user->name }}" disabled>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" value="{{ $laporan->tanggal }}"
                                class="form-control @error('tanggal') is-invalid @enderror" required>
                            @error('tanggal') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="waktu" class="form-label">Waktu</label>
                            <input type="time" name="waktu" id="waktu" value="{{ $laporan->waktu }}"
                                class="form-control @error('waktu') is-invalid @enderror" required>
                            @error('waktu') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="sektor" class="form-label">Sektor</label>
                        <input type="text" name="sektor" id="sektor" value="{{ $laporan->sektor }}"
                            class="form-control @error('sektor') is-invalid @enderror" required>
                        @error('sektor') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="uraian_kegiatan" class="form-label">Uraian Kegiatan</label>
                        <textarea name="uraian_kegiatan" id="uraian_kegiatan" rows="3"
                            class="form-control @error('uraian_kegiatan') is-invalid @enderror" required>{{ $laporan->uraian_kegiatan }}</textarea>
                        @error('uraian_kegiatan') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="dokumentasi" class="form-label">Dokumentasi (Foto)</label>
                        @if($laporan->dokumentasi)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$laporan->dokumentasi) }}" alt="Dokumentasi"
                                style="max-width: 150px; max-height: 150px; object-fit: cover;">
                        </div>
                        @endif
                        <input type="file" name="dokumentasi" id="dokumentasi"
                            class="form-control @error('dokumentasi') is-invalid @enderror">
                        @error('dokumentasi') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="saksi" class="form-label">Saksi</label>
                        <select name="saksi" id="saksi" class="form-control @error('saksi') is-invalid @enderror" required>
                            <option value="">-- Pilih Saksi --</option>
                            @foreach ($user as $u)
                            <option value="{{ $u->name }}" {{ $laporan->saksi == $u->name ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('saksi') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-simpan">Simpan</button>
                    <a href="{{ route('laporan.index') }}" class="btn btn-batal">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection