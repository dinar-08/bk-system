<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 16px;
        }

        h1 {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .sub {
            color: #64748b;
            font-size: 10px;
            margin-bottom: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #1d4ed8;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }

        td {
            padding: 5px 8px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        tr:nth-child(even) td {
            background: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-green {
            background: #dcfce7;
            color: #166534;
        }

        .badge-blue {
            background: #dbeafe;
            color: #1e40af;
        }
    </style>
</head>

<body>
    <h1>Rekap Semua Kasus BK — SD Al Kautsar</h1>
    <p class="sub">Dicetak: {{ now()->format('d M Y, H:i') }} WIB · Total: {{ $laporan->count() }} kasus</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Guru BK</th>
                <th>Judul Laporan</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $i => $l)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $l->siswa->nis ?? '-' }}</td>
                    <td>{{ $l->siswa->nama_siswa ?? '-' }}</td>
                    <td>{{ $l->siswa->kelas ?? '-' }}</td>
                    <td>{{ $l->guruBk->nama ?? '-' }}</td>
                    <td>{{ $l->judul_laporan }}</td>
                    <td>{{ $l->kategori ?? '-' }}</td>
                    <td><span
                            class="badge badge-{{ in_array($l->status, ['selesai', 'dirujuk']) ? 'green' : 'blue' }}">{{ ucfirst($l->status) }}</span>
                    </td>
                    <td>{{ $l->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
BLADE