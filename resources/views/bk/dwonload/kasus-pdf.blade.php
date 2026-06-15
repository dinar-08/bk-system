<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }

        h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .sub {
            color: #64748b;
            font-size: 11px;
            margin-bottom: 16px;
        }

        .section {
            margin-bottom: 16px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .section-title {
            background: #1d4ed8;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
            padding: 6px 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 5px 12px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }

        td:first-child {
            font-weight: 500;
            color: #64748b;
            width: 35%;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
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
            margin-top: 24px;
            font-size: 10px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>Laporan Kasus BK — {{ $laporan->judul_laporan }}</h1>
    <p class="sub">Dicetak: {{ now()->format('d M Y, H:i') }} WIB · SD Al Kautsar</p>

    <div class="section">
        <div class="section-title">Data Siswa</div>
        <table>
            <tr>
                <td>Nama</td>
                <td>{{ $laporan->siswa->nama_siswa ?? '-' }}</td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>{{ $laporan->siswa->nis ?? '-' }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>{{ $laporan->siswa->kelas ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>{{ $laporan->siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td>Nama Orang Tua</td>
                <td>{{ $laporan->siswa->nama_ortu ?? '-' }}</td>
            </tr>
            <tr>
                <td>No. WA</td>
                <td>{{ $laporan->siswa->no_whatsapp ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Detail Laporan</div>
        <table>
            <tr>
                <td>Guru BK</td>
                <td>{{ $laporan->guruBk->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>{{ $laporan->kategori ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jenis Masalah</td>
                <td>{{ $laporan->jenis_masalah ?? '-' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td><span
                        class="badge badge-{{ $laporan->status === 'selesai' ? 'green' : 'blue' }}">{{ ucfirst($laporan->status) }}</span>
                </td>
            </tr>
            <tr>
                <td>Tanggal Laporan</td>
                <td>{{ $laporan->created_at->format('d M Y') }}</td>
            </tr>
            <tr>
                <td>Deskripsi</td>
                <td>{{ $laporan->deskripsi }}</td>
            </tr>
        </table>
    </div>

    @if($laporan->pemanggilan->isNotEmpty())
        <div class="section">
            <div class="section-title">Riwayat Pemanggilan</div>
            <table>
                @foreach($laporan->pemanggilan as $p)
                    <tr>
                        <td>Tanggal</td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal_pemanggilan)->format('d M Y') }} · {{ $p->waktu_pemanggilan }}
                        </td>
                    </tr>
                    <tr>
                        <td>Tujuan</td>
                        <td>{{ $p->tujuan }}</td>
                    </tr>
                    <tr>
                        <td>Status Hadir</td>
                        <td>{{ str_replace('_', ' ', ucfirst($p->status_kehadiran)) }}</td>
                    </tr>
                    <tr>
                        <td>Catatan</td>
                        <td>{{ $p->catatan ?? '-' }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    @if($laporan->monitoring->isNotEmpty())
        <div class="section">
            <div class="section-title">Riwayat Monitoring</div>
            <table>
                @foreach($laporan->monitoring as $m)
                    <tr>
                        <td>Monitoring ke-{{ $m->monitoring_ke }}</td>
                        <td>{{ \Carbon\Carbon::parse($m->tanggal_monitoring)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>{{ str_replace('_', ' ', ucfirst($m->status_perkembangan)) }}</td>
                    </tr>
                    <tr>
                        <td>Catatan</td>
                        <td>{{ $m->catatan_perkembangan ?? '-' }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    @if($laporan->evaluasi)
        <div class="section">
            <div class="section-title">Evaluasi</div>
            <table>
                <tr>
                    <td>Hasil Evaluasi</td>
                    <td>{{ $laporan->evaluasi->hasil_evaluasi ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Status Akhir</td>
                    <td>{{ ucfirst($laporan->evaluasi->status_akhir ?? '-') }}</td>
                </tr>
            </table>
        </div>
    @endif

    <div class="footer">Dokumen ini dicetak otomatis oleh Sistem Laporan BK SD Al Kautsar</div>
</body>

</html>