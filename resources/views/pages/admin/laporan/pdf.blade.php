<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .nama {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
            padding: 6px;
        }

        td {
            padding: 5px;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

    </style>
</head>
<body>

<div class="title">LAPORAN KEGIATAN</div>

<div class="nama">
    <strong>Nama:</strong> {{ $namaPembuat }}
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>RPH</th>
            <th>Uraian Kegiatan</th>
            <th>Saksi</th>
            <th>Status</th>
            <th>TTD</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($laporan as $item)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
            <td>{{ $item->waktu }}</td>
            <td>{{ $item->sektor }}</td>
            <td>{{ $item->uraian_kegiatan }}</td>
            <td>{{ $item->saksi }}</td>
            <td class="text-center">{{ ucfirst($item->status) }}</td>
            <td class="paraf"></td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
