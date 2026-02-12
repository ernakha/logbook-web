@extends('layouts.master')
@section('content')
<style>
    .btn-tambah {
        background-color: #06923E;
        color: #fff;
        border: none;
    }

    .btn-tambah:hover {
        background-color: #2aa15a;
        color: #fff;
    }
</style>
<div class="row">
    <div class="col-lg-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold mb-0">Laporan</h5>
                </div>
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 align-middle">
                        <thead class="text-dark fs-4">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>RPH</th>
                                <th>Uraian Kegiatan</th>
                                <th>Saksi</th>
                                <th>Dokumentasi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->tanggal }}</td>
                                <td>{{ $item->waktu }}</td>
                                <td>{{ $item->sektor }}</td>
                                <td>{{ $item->uraian_kegiatan }}</td>
                                <td>{{ $item->saksi }}</td>
                                <td>
                                    @if ($item->dokumentasi)
                                    <img src="{{ asset('storage/'.$item->dokumentasi) }}"
                                        alt="Dokumentasi"
                                        style="max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 6px;">
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-warning">{{ ucfirst($item->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">Belum ada data laporan</td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection