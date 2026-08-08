<?php
namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DownloadController extends Controller
{
    public function downloadKasusPdf(string $id)
    {
        $laporan = Laporan::with([
            'siswa',
            'guruBk',
            'pemanggilan',
            'monitoring.guruBk',
            'evaluasi'
        ])->findOrFail($id);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('bk.download.kasus-pdf', compact('laporan'));
        $pdf->setPaper('A4', 'portrait');

        $namaFile = 'kasus-' . str()->slug($laporan->siswa->nama_siswa ?? 'siswa') . '-' . now()->format('Ymd') . '.pdf';
        return $pdf->download($namaFile);
    }

    public function downloadSemuaExcel(Request $request)
    {
        $laporan = Laporan::with(['siswa', 'guruBk', 'evaluasi'])->get();

        $headers = ['Content-Type' => 'application/vnd.ms-excel'];
        return response()->streamDownload(function () use ($laporan) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Semua Kasus');

            $head = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Guru BK', 'Judul', 'Kategori', 'Status', 'Tanggal Buat'];
            $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

            foreach ($head as $i => $h) {
                $sheet->setCellValue($columns[$i] . '1', $h);
            }
            $sheet->getStyle('A1:I1')->getFont()->setBold(true);

            foreach ($laporan as $i => $l) {
                $r = $i + 2;
                $sheet->setCellValue('A' . $r, $i + 1);
                $sheet->setCellValue('B' . $r, $l->siswa->nis ?? '-');
                $sheet->setCellValue('C' . $r, $l->siswa->nama_siswa ?? '-');
                $sheet->setCellValue('D' . $r, $l->siswa->kelas ?? '-');
                $sheet->setCellValue('E' . $r, $l->guruBk->nama ?? '-');
                $sheet->setCellValue('F' . $r, $l->judul_laporan);
                $sheet->setCellValue('G' . $r, $l->kategori ?? '-');
                $sheet->setCellValue('H' . $r, $l->status);
                $sheet->setCellValue('I' . $r, $l->created_at->format('d/m/Y'));
            }

            foreach (range('A', 'I') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, 'semua-kasus-bk-' . now()->format('Ymd') . '.xlsx', $headers);
    }

    public function downloadSemuaPdf()
    {
        $laporan = Laporan::with(['siswa', 'guruBk', 'evaluasi'])->latest()->get();

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('bk.download.semua-pdf', compact('laporan'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('semua-kasus-bk-' . now()->format('Ymd') . '.pdf');
    }
}

