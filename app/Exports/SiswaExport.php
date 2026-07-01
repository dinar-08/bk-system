<?php

namespace App\Exports;

use App\Models\Laporan;
use App\Models\Siswa;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SiswaExport
{
    public function __construct(
        private string $tipe = 'siswa',
        private ?string $kelas = null,
        private ?string $status = null,
    ) {
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
            ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Password Awal']
        ], null, 'A1');

        $siswa = Siswa::with('user')
            ->when($this->kelas, function ($query) {
                $query->where('kelas', $this->kelas);
            })
            ->when($this->status, function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->where('status_akun', $this->status);
                });
            })
            ->orderBy('kelas')
            ->orderBy('nama_siswa')
            ->get();

        $data = [];

        foreach ($siswa as $i => $s) {
            $data[] = [
                $i + 1,
                $s->nis,
                $s->nama_siswa,
                $s->kelas,
                $s->user->default_password ?? '-',
            ];
        }

        if (!empty($data)) {
            $sheet->fromArray($data, null, 'A2');
        }

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        foreach (range('A', 'E') as $col) {
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