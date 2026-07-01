<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Akun Siswa</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        .kop {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .kop td {
            border: none;
            vertical-align: middle;
        }

        .logo {
            width: 120px;
            height: auto;
        }

        .kop-title {
            text-align: center;
        }

        .kop-title h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-title h2 {
            margin: 4px 0;
            font-size: 15px;
            font-weight: bold;
        }

        .kop-title p {
            margin: 0;
            font-size: 11px;
            color: #6b7280;
        }

        .line {
            border-top: 2px solid #111827;
            margin-top: 10px;
            margin-bottom: 18px;
        }

        .judul {
            text-align: center;
            margin-bottom: 18px;
        }

        .judul h3 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th {
            background: #1d4ed8;
            color: white;
            padding: 8px;
            border: 1px solid #d1d5db;
            text-align: left;
        }

        table.data td {
            padding: 8px;
            border: 1px solid #d1d5db;
        }

        table.data tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .no {
            width: 40px;
            text-align: center;
        }

        .password {
            color: #dc2626;
            font-weight: bold;
        }

        .muted {
            color: #6b7280;
            font-style: italic;
        }

        .note {
            margin-top: 18px;
            font-size: 10px;
            color: #4b5563;
            line-height: 1.6;
        }

        .footer {
            width: 100%;
            margin-top: 40px;
        }

        .footer td {
            border: none;
        }

        .ttd {
            width: 220px;
            text-align: center;
        }
    </style>
</head>

<body>

    {{-- KOP --}}
    <table class="kop">
        <tr>
            @php
                $logoPath = public_path('asset/logo.png');
                $logoBase64 = null;

                if (file_exists($logoPath)) {
                    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                }
            @endphp

            @if($logoBase64)
                <img src="{{ $logoBase64 }}" class="logo">
            @endif

            <td class="kop-title">
                <h1>Sistem Informasi Bimbingan dan Konseling</h1>
                <h2>SDIT Muhammadiyah Al-Kautsar</h2>
                <p>Data Akun Siswa</p>
            </td>

            <td width="80"></td>
        </tr>
    </table>

    <div class="line"></div>

    {{-- TABEL --}}
    <table class="data">
        <thead>
            <tr>
                <th class="no">No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Password Awal</th>
            </tr>
        </thead>

        <tbody>
            @forelse($siswa as $index => $item)
                <tr>
                    <td class="no">{{ $index + 1 }}</td>
                    <td>{{ $item->nis }}</td>
                    <td>{{ $item->nama_siswa }}</td>
                    <td>{{ $item->kelas }}</td>

                    <td>
                        @if($item->user && $item->user->default_password)
                            <span class="password">
                                {{ $item->user->default_password }}
                            </span>
                        @else
                            <span class="muted">
                                Sudah Diganti
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">
                        Tidak ada data siswa.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- CATATAN --}}
    <div class="note">
        <strong>Catatan:</strong><br>
        • Password harap Diganti saat sudah login pertama kali.<br>
    {{-- TTD --}}
    <table class="footer">
        <tr>
            <td></td>

            <td class="ttd">
                Kartasura, {{ now('Asia/Jakarta')->translatedFormat('d F Y') }}

                <br><br><br><br>

                <strong>Administrator BK</strong>
            </td>
        </tr>
    </table>

</body>

</html>