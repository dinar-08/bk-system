<?php
namespace App\Exports;

use App\Models\Siswa;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SiswaExport
{
    public function __construct(private string $tipe = 'siswa')
    {
    }

    public function download(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = ['Content-Type' => 'application/vnd.ms-excel'];
        $tipe = $this->tipe;

        return response()->streamDownload(function () use ($tipe) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            if ($tipe === 'siswa') {
                $this->exportDataSiswa($sheet);
            } else {
                $this->exportLengkap($sheet, $spreadsheet);
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, 'data-siswa-' . $tipe . '-' . now()->format('Ymd') . '.xlsx', $headers);
    }

    private function exportDataSiswa($sheet): void
    {
        $heads = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Jenis Kelamin', 'Tanggal Lahir', 'Nama Ortu', 'No WA', 'Status Akun'];
        foreach ($heads as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
        }
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $siswa = Siswa::with('user')->orderBy('kelas')->orderBy('nama_siswa')->get();
        foreach ($siswa as $i => $s) {
            $r = $i + 2;
            $sheet->setCellValueByColumnAndRow(1, $r, $i + 1);
            $sheet->setCellValueByColumnAndRow(2, $r, $s->nis);
            $sheet->setCellValueByColumnAndRow(3, $r, $s->nama_siswa);
            $sheet->setCellValueByColumnAndRow(4, $r, $s->kelas);
            $sheet->setCellValueByColumnAndRow(5, $r, $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValueByColumnAndRow(6, $r, $s->tanggal_lahir ?? '-');
            $sheet->setCellValueByColumnAndRow(7, $r, $s->nama_ortu ?? '-');
            $sheet->setCellValueByColumnAndRow(8, $r, $s->no_whatsapp ?? '-');
            $sheet->setCellValueByColumnAndRow(9, $r, $s->user->status_akun ?? 'aktif');
        }

        for ($c = 1; $c <= 9; $c++)
            $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
    }

    private function exportLengkap($sheet, $spreadsheet): void
    {
        $this->exportDataSiswa($sheet);

        // Sheet 2: riwayat kasus
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Riwayat Kasus');

        $heads = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Judul Laporan', 'Kategori', 'Status', 'Guru BK', 'Tanggal'];
        foreach ($heads as $i => $h)
            $sheet2->setCellValueByColumnAndRow($i + 1, 1, $h);
        $sheet2->getStyle('A1:I1')->getFont()->setBold(true);

        $laporan = \App\Models\Laporan::with(['siswa', 'guruBk'])->orderBy('created_at', 'desc')->get();
        foreach ($laporan as $i => $l) {
            $r = $i + 2;
            $sheet2->setCellValueByColumnAndRow(1, $r, $i + 1);
            $sheet2->setCellValueByColumnAndRow(2, $r, $l->siswa->nis ?? '-');
            $sheet2->setCellValueByColumnAndRow(3, $r, $l->siswa->nama_siswa ?? '-');
            $sheet2->setCellValueByColumnAndRow(4, $r, $l->siswa->kelas ?? '-');
            $sheet2->setCellValueByColumnAndRow(5, $r, $l->judul_laporan);
            $sheet2->setCellValueByColumnAndRow(6, $r, $l->kategori ?? '-');
            $sheet2->setCellValueByColumnAndRow(7, $r, $l->status);
            $sheet2->setCellValueByColumnAndRow(8, $r, $l->guruBk->nama ?? 'Belum ditangani');
            $sheet2->setCellValueByColumnAndRow(9, $r, $l->created_at->format('d/m/Y'));
        }
        for ($c = 1; $c <= 9; $c++)
            $sheet2->getColumnDimensionByColumn($c)->setAutoSize(true);
    }
}