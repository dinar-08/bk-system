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
            margin-bottom: 12px;
            border-bottom: 2px solid #1d4ed8;
            padding-bottom: 8px;
        }

        .kop td {
            border: none;
            background: transparent !important;
            padding: 0;
        }

        .logo {
            width: 85px;
            height: auto;
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
            text-align: center;
            margin: 10px 0 3px;
        }

        .sub {
            color: #64748b;
            font-size: 10px;
            margin-bottom: 14px;
            text-align: center;
        }

        .section {
            margin-bottom: 14px;
            border: 1px solid #e2e8f0;
        }

        .section-title {
            background: #1d4ed8;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
            padding: 6px 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 5px 9px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }

        td:first-child {
            font-weight: bold;
            color: #64748b;
            width: 28%;
        }

        th {
            background: #f1f5f9;
            color: #334155;
            font-size: 9px;
            text-align: left;
        }

        .foto {
            width: 95px;
            height: 115px;
            object-fit: cover;
            border: 1px solid #cbd5e1;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-green {
            background: #dcfce7;
            color: #166534;
        }

        .badge-red {
            background: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
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

        $fotoPath = optional($laporan->siswa)->foto
            ? storage_path('app/public/' . $laporan->siswa->foto)
            : null;

        $fotoBase64 = null;

        if ($fotoPath && file_exists($fotoPath)) {
            $extension = strtolower(pathinfo($fotoPath, PATHINFO_EXTENSION));
            $mime = in_array($extension, ['jpg', 'jpeg']) ? 'jpeg' : $extension;
            $fotoBase64 = 'data:image/' . $mime . ';base64,' . base64_encode(file_get_contents($fotoPath));
        }

        $statusAkhir = optional($laporan->evaluasi)->status_akhir ?? $laporan->status;
    @endphp

    <table class="kop">
        <tr>
            <td style="width: 90px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo">
                @endif
            </td>

            <td class="kop-title">
                <h2>SDIT MUHAMMADIYAH AL-KAUTSAR</h2>
                <p>Jl. Garuda Mas, Gonilan, Kartasura, Sukoharjo</p>
                <p>Jawa Tengah</p>
            </td>

            <td style="width: 90px;"></td>
        </tr>
    </table>

    <h1>LAPORAN DETAIL PERMASALAHAN SISWA</h1>
    <p class="sub">
        Dicetak: {{ now('Asia/Jakarta')->format('d M Y, H:i') }} WIB
    </p>

    <div class="section">
        <div class="section-title">Data Siswa</div>
        <table>
            <tr>
                <td>Foto Siswa</td>
                <td>
                    @if($fotoBase64)
                        <img src="{{ $fotoBase64 }}" class="foto">
                    @else
                        Tidak ada foto
                    @endif
                </td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>{{ optional($laporan->siswa)->nama_siswa ?? '-' }}</td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>{{ optional($laporan->siswa)->nis ?? '-' }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>{{ optional($laporan->siswa)->kelas ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>
                    {{ optional($laporan->siswa)->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                </td>
            </tr>
            <tr>
                <td>Tanggal Lahir</td>
                <td>
                    {{ optional($laporan->siswa)->tanggal_lahir
                        ? \Carbon\Carbon::parse($laporan->siswa->tanggal_lahir)->format('d M Y')
                        : '-' }}
                </td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>{{ optional($laporan->siswa)->alamat ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama Orang Tua</td>
                <td>{{ optional($laporan->siswa)->nama_ortu ?? '-' }}</td>
            </tr>
            <tr>
                <td>No. WhatsApp</td>
                <td>{{ optional($laporan->siswa)->no_whatsapp ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detail Laporan Permasalahan</div>
        <table>
            <tr>
                <td>Judul Laporan</td>
                <td>{{ $laporan->judul_laporan ?? '-' }}</td>
            </tr>
            <tr>
                <td>Guru BK</td>
                <td>{{ optional($laporan->guruBk)->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>{{ ucfirst($laporan->kategori ?? '-') }}</td>
            </tr>
            <tr>
                <td>Jenis Masalah</td>
                <td>{{ $laporan->jenis_masalah ?? '-' }}</td>
            </tr>
            <tr>
                <td>Status Akhir</td>
                <td>
                    <span class="badge badge-{{ $statusAkhir === 'selesai' ? 'green' : 'red' }}">
                        {{ ucfirst($statusAkhir ?? '-') }}
                    </span>
                </td>
            </tr>
            <tr>
                <td>Tanggal Laporan Masuk</td>
                <td>{{ optional($laporan->created_at)->format('d M Y') ?? '-' }}</td>
            </tr>
            <tr>
                <td>Deskripsi</td>
                <td>{{ $laporan->deskripsi ?? '-' }}</td>
            </tr>
            <tr>
                <td>Bukti / Lampiran</td>
                <td>{{ $laporan->bukti ? $laporan->bukti : 'Tidak ada' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Riwayat Pemanggilan</div>

        @if($laporan->pemanggilan->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Pihak Dipanggil</th>
                        <th>Tujuan</th>
                        <th>Status Kehadiran</th>
<<<<<<< HEAD
                        <th>Tindak Lanjut</th>
=======
>>>>>>> 4226421 (backup)
                        <th>Tanggal Monitoring</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan->pemanggilan as $p)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $p->tanggal_pemanggilan ? \Carbon\Carbon::parse($p->tanggal_pemanggilan)->format('d M Y') : '-' }}</td>
                            <td>{{ $p->waktu_pemanggilan ?? '-' }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($p->pihak_dipanggil ?? '-')) }}</td>
                            <td>{{ $p->tujuan ?? '-' }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($p->status_kehadiran ?? '-')) }}</td>
<<<<<<< HEAD
                            <td>{{ str_replace('_', ' ', ucfirst($p->tindak_lanjut ?? '-')) }}</td>
=======
>>>>>>> 4226421 (backup)
                            <td>{{ $p->tanggal_monitoring ? \Carbon\Carbon::parse($p->tanggal_monitoring)->format('d M Y') : '-' }}</td>
                            <td>{{ $p->catatan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <table>
                <tr>
                    <td>Data</td>
                    <td>Belum ada riwayat pemanggilan.</td>
                </tr>
            </table>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Riwayat Monitoring</div>

        @if($laporan->monitoring->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Monitoring Ke</th>
                        <th>Tanggal</th>
                        <th>Status Monitoring</th>
                        <th>Status Perkembangan</th>
                        <th>Catatan Perkembangan</th>
<<<<<<< HEAD
                        <th>Tindak Lanjut</th>
=======
>>>>>>> 4226421 (backup)
                        <th>Monitoring Berikutnya</th>
                        <th>Guru BK</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan->monitoring->sortBy('monitoring_ke') as $m)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $m->monitoring_ke ?? '-' }}</td>
                            <td>{{ $m->tanggal_monitoring ? \Carbon\Carbon::parse($m->tanggal_monitoring)->format('d M Y') : '-' }}</td>
                            <td>{{ ucfirst($m->status_monitoring ?? '-') }}</td>
                            <td>{{ ucfirst($m->status_perkembangan ?? '-') }}</td>
                            <td>{{ $m->catatan_perkembangan ?? '-' }}</td>
<<<<<<< HEAD
                            <td>{{ $m->tindak_lanjut ?? '-' }}</td>
=======
>>>>>>> 4226421 (backup)
                            <td>{{ $m->tanggal_monitoring_berikutnya ? \Carbon\Carbon::parse($m->tanggal_monitoring_berikutnya)->format('d M Y') : '-' }}</td>
                            <td>{{ optional($m->guruBk)->nama ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <table>
                <tr>
                    <td>Data</td>
                    <td>Belum ada riwayat monitoring.</td>
                </tr>
            </table>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Evaluasi Akhir</div>

        @if($laporan->evaluasi)
            <table>
                <tr>
                    <td>Tanggal Evaluasi</td>
                    <td>
                        {{ $laporan->evaluasi->tanggal_evaluasi
                            ? \Carbon\Carbon::parse($laporan->evaluasi->tanggal_evaluasi)->format('d M Y')
                            : '-' }}
                    </td>
                </tr>
                <tr>
                    <td>Hasil Evaluasi</td>
                    <td>{{ $laporan->evaluasi->hasil_evaluasi ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Status Akhir</td>
                    <td>{{ ucfirst($laporan->evaluasi->status_akhir ?? '-') }}</td>
                </tr>
            </table>
        @else
            <table>
                <tr>
                    <td>Data</td>
                    <td>Belum ada data evaluasi.</td>
                </tr>
            </table>
        @endif
    </div>

    <div class="footer">
        Dokumen ini dicetak otomatis oleh Sistem Laporan BK SDIT Muhammadiyah Al-Kautsar
    </div>
</body>

</html>