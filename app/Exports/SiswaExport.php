<?php

namespace App\Exports;

use App\Models\Siswa;
use App\Models\Laporan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SiswaExport
{
    public function __construct(private string $tipe = 'siswa')
    {
    }

    public function download()
    {
        return response()->streamDownload(function () {
            $spreadsheet = new Spreadsheet();

            $this->exportDataSiswa($spreadsheet->getActiveSheet());

            if ($this->tipe === 'lengkap') {
                $this->exportRiwayatKasus($spreadsheet);
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, 'data-siswa-' . $this->tipe . '-' . now()->format('Ymd-His') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function exportDataSiswa($sheet): void
    {
        $sheet->setTitle('Data Siswa');

        $sheet->fromArray([
            ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Jenis Kelamin', 'Tanggal Lahir', 'Nama Ortu', 'No WA', 'Status Akun']
        ], null, 'A1');

        $siswa = Siswa::with('user')->orderBy('kelas')->orderBy('nama_siswa')->get();

        $data = [];

        foreach ($siswa as $i => $s) {
            $data[] = [
                $i + 1,
                $s->nis,
                $s->nama_siswa,
                $s->kelas,
                $s->jenis_kelamin === 'L' ? 'Laki-laki' : ($s->jenis_kelamin === 'P' ? 'Perempuan' : '-'),
                $s->tanggal_lahir ? $s->tanggal_lahir->format('d/m/Y') : '-',
                $s->nama_ortu ?? '-',
                $s->no_whatsapp ?? '-',
                $s->user->status_akun ?? 'aktif',
            ];
        }

        if (!empty($data)) {
            $sheet->fromArray($data, null, 'A2');
        }

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function exportRiwayatKasus($spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Riwayat Kasus');

        $sheet->fromArray([
            ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Judul Laporan', 'Kategori', 'Status', 'Guru BK', 'Tanggal']
        ], null, 'A1');

        $laporan = Laporan::with(['siswa', 'guruBk'])->latest()->get();

        $data = [];

        foreach ($laporan as $i => $l) {
            $data[] = [
                $i + 1,
                $l->siswa->nis ?? '-',
                $l->siswa->nama_siswa ?? '-',
                $l->siswa->kelas ?? '-',
                $l->judul_laporan,
                $l->kategori ?? '-',
                $l->status,
                $l->guruBk->nama ?? 'Belum ditangani',
                $l->created_at ? $l->created_at->format('d/m/Y') : '-',
            ];
        }

        if (!empty($data)) {
            $sheet->fromArray($data, null, 'A2');
        }

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}