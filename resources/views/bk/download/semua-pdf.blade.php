<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 16px;
        }

        .kop {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 2px solid #1d4ed8;
            padding-bottom: 8px;
        }

        .kop td {
            border: none;
            background: transparent !important;
            padding: 0;
        }

        .logo {
            width: 200px;
        }

        .kop-title {
            text-align: center;
        }

        .kop-title h2 {
            margin: 0;
            font-size: 17px;
            color: #0f172a;
        }

        .kop-title p {
            margin: 2px 0;
            font-size: 10px;
            color: #475569;
        }

        h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
            text-align: center;
        }

        .sub {
            color: #64748b;
            font-size: 10px;
            margin-bottom: 14px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #1d4ed8;
            color: #fff;
            padding: 6px 7px;
            text-align: left;
            font-size: 9px;
        }

        td {
            padding: 5px 7px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .kelas-row td {
            background: #dbeafe !important;
            color: #1e3a8a;
            font-weight: bold;
            font-size: 11px;
        }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 20px;
            font-size: 8px;
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

        .footer {
            margin-top: 14px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>

<body>

    @php
        $logoPath = public_path('asset/logo.png');
        $logoBase64 = null;

        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
    @endphp

    <table class="kop">
        <tr>
            <td style="width: 75px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo">
                @endif
            </td>

            <td class="kop-title">
                <h2>SDIT MUHAMMADIYAH AL-KAUTSAR</h2>
                <p>Jl. Garuda Mas, Gonilan, Kartasura, Sukoharjo</p>
                <p>Jawa Tengah</p>
            </td>

            <td style="width: 75px;"></td>
        </tr>
    </table>

    <h1>REKAP RIWAYAT PERMASALAHAN SISWA</h1>
    <p class="sub">
        Dicetak: {{ now('Asia/Jakarta')->format('d M Y, H:i') }} WIB · Total: {{ $laporan->count() }} kasus
    </p>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 8%;">NIS</th>
                <th style="width: 17%;">Nama Siswa</th>
                <th style="width: 9%;">Guru BK</th>
                <th style="width: 17%;">Judul Laporan</th>
                <th style="width: 9%;">Kategori</th>
                <th style="width: 9%;">Status</th>
                <th style="width: 10%;">Tanggal Masuk</th>
                <th style="width: 10%;">Tanggal Selesai</th>
            </tr>
        </thead>

        <tbody>
            @php
                $laporanPerKelas = $laporan
                    ->sortBy(fn($item) => optional($item->siswa)->nis)
                    ->groupBy(fn($item) => optional($item->siswa)->kelas ?? 'Tanpa Kelas');
            @endphp

            @forelse($laporanPerKelas as $kelas => $dataKelas)
                <td colspan="9">
                    KELAS {{ $kelas }} ({{ $dataKelas->count() }} Kasus)
                </td>

                @foreach($dataKelas as $i => $l)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ optional($l->siswa)->nis ?? '-' }}</td>
                        <td>{{ optional($l->siswa)->nama_siswa ?? '-' }}</td>
                        <td>{{ optional($l->guruBk)->nama ?? '-' }}</td>
                        <td>{{ $l->judul_laporan ?? '-' }}</td>
                        <td>{{ ucfirst($l->kategori ?? '-') }}</td>
                        <td>
                            <span class="badge badge-{{ in_array($l->status, ['selesai', 'dirujuk']) ? 'green' : 'blue' }}">
                                {{ ucfirst($l->status ?? '-') }}
                            </span>
                        </td>
                        <td>{{ optional($l->created_at)->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            {{ optional($l->evaluasi)->tanggal_evaluasi
                    ? \Carbon\Carbon::parse($l->evaluasi->tanggal_evaluasi)->format('d/m/Y')
                    : '-' }}
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;">Tidak ada data riwayat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dicetak otomatis oleh Sistem Laporan BK SDIT Muhammadiyah Al-Kautsar
    </div>

</body>

</html>