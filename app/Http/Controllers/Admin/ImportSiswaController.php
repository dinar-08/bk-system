<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportSiswaController extends Controller
{
    public function index()
    {
        return view('admin.import-siswa.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        $path = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Skip header row
        array_shift($rows);

        $berhasil = [];
        $gagal = [];

        foreach ($rows as $no => $row) {
            $rowNum = $no + 2; // +2 karena header di baris 1

            $nis = trim($row['A'] ?? '');
            $nama = trim($row['B'] ?? '');
            $kelas = trim($row['C'] ?? '');

            // Validasi dasar
            if (empty($nis) || empty($nama) || empty($kelas)) {
                $gagal[] = ['baris' => $rowNum, 'nis' => $nis ?: '-', 'nama' => $nama ?: '-', 'alasan' => 'Kolom NIS, Nama, atau Kelas kosong'];
                continue;
            }

            if (User::where('username', $nis)->exists() || Siswa::where('nis', $nis)->exists()) {
                $gagal[] = ['baris' => $rowNum, 'nis' => $nis, 'nama' => $nama, 'alasan' => 'NIS sudah terdaftar'];
                continue;
            }

            try {
                DB::transaction(function () use ($nis, $nama, $kelas, &$berhasil) {
                    $password = $this->generatePassword();

                    $user = User::create([
                        'name' => $nama,
                        'username' => $nis,
                        'role' => 'orang_tua',
                        'password' => Hash::make($password),
                        'must_change_password' => true,
                    ]);

                    Siswa::create([
                        'user_id' => $user->id,
                        'nis' => $nis,
                        'nama_siswa' => $nama,
                        'kelas' => $kelas,
                    ]);

                    $berhasil[] = ['nis' => $nis, 'nama' => $nama, 'kelas' => $kelas, 'password' => $password];
                });
            } catch (\Exception $e) {
                $gagal[] = ['baris' => $rowNum, 'nis' => $nis, 'nama' => $nama, 'alasan' => 'Error sistem: ' . $e->getMessage()];
            }
        }

        // Simpan hasil ke session untuk ditampilkan + bisa didownload
        session(['import_hasil' => ['berhasil' => $berhasil, 'gagal' => $gagal]]);

        return redirect()->route('admin.import-siswa.index')
            ->with('import_selesai', true);
    }

    public function downloadTemplate()
    {
        $headers = ['Content-Type' => 'application/vnd.ms-excel'];
        return response()->streamDownload(function () {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Siswa');
            $sheet->setCellValue('A1', 'NIS');
            $sheet->setCellValue('B1', 'Nama Siswa');
            $sheet->setCellValue('C1', 'Kelas');
            // Contoh data
            $sheet->setCellValue('A2', '12345');
            $sheet->setCellValue('B2', 'Nama Siswa Contoh');
            $sheet->setCellValue('C2', '1A');

            // Style header
            $sheet->getStyle('A1:C1')->getFont()->setBold(true);
            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(10);

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, 'template-import-siswa.xlsx', $headers);
    }

    public function downloadHasil()
    {
        $hasil = session('import_hasil', ['berhasil' => [], 'gagal' => []]);
        $berhasil = $hasil['berhasil'];

        if (empty($berhasil)) {
            return back()->with('error', 'Tidak ada data hasil import untuk didownload.');
        }

        $headers = ['Content-Type' => 'application/vnd.ms-excel'];
        return response()->streamDownload(function () use ($berhasil) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Hasil Import');
            $sheet->setCellValue('A1', 'NIS');
            $sheet->setCellValue('B1', 'Nama Siswa');
            $sheet->setCellValue('C1', 'Kelas');
            $sheet->setCellValue('D1', 'Password Awal');
            $sheet->getStyle('A1:D1')->getFont()->setBold(true);
            $sheet->getStyle('D1')->getFont()->setColor(
                (new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED))
            );

            foreach ($berhasil as $i => $row) {
                $r = $i + 2;
                $sheet->setCellValue("A{$r}", $row['nis']);
                $sheet->setCellValue("B{$r}", $row['nama']);
                $sheet->setCellValue("C{$r}", $row['kelas']);
                $sheet->setCellValue("D{$r}", $row['password']);
            }

            foreach (['A', 'B', 'C', 'D'] as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, 'hasil-import-siswa-' . now()->format('Ymd-His') . '.xlsx', $headers);
    }

    private function generatePassword(): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle(str_repeat($chars, 4)), 0, 8);
    }
}

