<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeUpdate;
use App\Models\Siswa;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
<<<<<<< HEAD
use Barryvdh\DomPDF\Facade\Pdf;

class ImportSiswaController extends Controller
{

=======

class ImportSiswaController extends Controller
{
>>>>>>> 4226421 (backup)
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        $path = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return back()->withErrors([
                'file' => 'File Excel harus memiliki header dan minimal satu baris data.',
            ]);
        }

        $header = null;
        $barisHeader = null;
<<<<<<< HEAD
=======
        $kolom = [
            'nis' => null,
            'nama' => null,
            'kelas' => null,
        ];
>>>>>>> 4226421 (backup)

        foreach ($rows as $index => $row) {
            $hasilDeteksi = $this->deteksiKolom($row);

            if (
                $hasilDeteksi['nis'] !== null &&
                $hasilDeteksi['nama'] !== null &&
                $hasilDeteksi['kelas'] !== null
            ) {
                $header = $row;
                $kolom = $hasilDeteksi;
                $barisHeader = $index;
                break;
            }
        }

        if ($header === null) {
            return back()->withErrors([
                'file' => 'Header Excel tidak ditemukan. Wajib ada kolom NIS, Nama Siswa/Nama, dan Kelas.',
            ]);
        }

        $rows = array_slice($rows, $barisHeader);
        array_shift($rows);

        if ($kolom['nis'] === null || $kolom['nama'] === null || $kolom['kelas'] === null) {
            return back()->withErrors([
                'file' => 'Header Excel tidak sesuai. Wajib ada kolom NIS, Nama Siswa/Nama, dan Kelas.',
            ]);
        }

        $berhasil = [];
        $gagal = [];

        $periodeAktif = PeriodeUpdate::aktifSekarang();

        $tahunAjaran = $periodeAktif
            ? $periodeAktif->tahun_ajaran
            : now()->year . '/' . (now()->year + 1);

        foreach ($rows as $no => $row) {
            $rowNum = $no + 2;

            $nis = trim((string) ($row[$kolom['nis']] ?? ''));
            $nama = trim((string) ($row[$kolom['nama']] ?? ''));
            $kelas = trim((string) ($row[$kolom['kelas']] ?? ''));

            if ($nis === '' && $nama === '' && $kelas === '') {
                continue;
            }

            if (empty($nis) || empty($nama) || empty($kelas)) {
                $gagal[] = [
                    'baris' => $rowNum,
                    'nis' => $nis ?: '-',
                    'nama' => $nama ?: '-',
                    'alasan' => 'Kolom NIS, Nama, atau Kelas kosong',
                ];
                continue;
            }

            if (User::where('username', $nis)->exists() || Siswa::where('nis', $nis)->exists()) {
                $gagal[] = [
                    'baris' => $rowNum,
                    'nis' => $nis,
                    'nama' => $nama,
                    'alasan' => 'NIS sudah terdaftar',
                ];
                continue;
            }

            try {
                DB::transaction(function () use ($nis, $nama, $kelas, $tahunAjaran, &$berhasil) {
                    $password = $this->generatePassword();

                    $user = User::create([
                        'name' => $nama,
                        'username' => $nis,
                        'role' => 'orang_tua',
                        'status_akun' => 'aktif',
                        'password' => Hash::make($password),
                        'default_password' => $password,
                        'must_change_password' => true,
                    ]);

                    Siswa::create([
                        'user_id' => $user->id,
                        'nis' => $nis,
                        'nama_siswa' => $nama,
                        'kelas' => $kelas,
                        'tahun_ajaran' => $tahunAjaran,
                    ]);

                    $berhasil[] = [
                        'nis' => $nis,
                        'nama' => $nama,
                        'kelas' => $kelas,
<<<<<<< HEAD
=======
                        'tahun_ajaran' => $tahunAjaran,
>>>>>>> 4226421 (backup)
                        'password' => $password,
                    ];
                });
            } catch (\Exception $e) {
                $gagal[] = [
                    'baris' => $rowNum,
                    'nis' => $nis,
                    'nama' => $nama,
                    'alasan' => 'Error sistem: ' . $e->getMessage(),
                ];
            }
        }

        session([
            'import_hasil' => [
                'berhasil' => $berhasil,
                'gagal' => $gagal,
            ],
        ]);

        return redirect()
            ->route('admin.siswa.index')
            ->with('import_selesai', true)
            ->with('import_hasil', [
                'berhasil' => $berhasil,
                'gagal' => $gagal,
            ]);
    }

    public function downloadHasil()
    {
        $hasil = session('import_hasil', [
            'berhasil' => [],
            'gagal' => [],
        ]);

        $berhasil = $hasil['berhasil'];

        if (empty($berhasil)) {
            return back()->with('error', 'Tidak ada data hasil import untuk didownload.');
        }

        $pdf = Pdf::loadView('admin.import-siswa.hasil', [
            'berhasil' => $berhasil,
            'tanggal' => now()->format('d-m-Y H:i'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download(
            'hasil-import-siswa-' . now()->format('Ymd-His') . '.pdf'
        );
    }

    private function deteksiKolom(array $header): array
    {
        $kolom = [
            'nis' => null,
            'nama' => null,
            'kelas' => null,
        ];

        foreach ($header as $key => $value) {
            $namaHeader = $this->normalisasiHeader($value);

            if (in_array($namaHeader, ['nis', 'nomorinduksiswa', 'nomorinduk'])) {
                $kolom['nis'] = $key;
            }

            if (in_array($namaHeader, ['nama', 'namasiswa', 'namalengkap', 'namalengkapsiswa'])) {
                $kolom['nama'] = $key;
            }

            if (in_array($namaHeader, ['kelas', 'rumbel', 'rombonganbelajar'])) {
                $kolom['kelas'] = $key;
            }
        }

        return $kolom;
    }

    private function normalisasiHeader($value): string
    {
        $value = strtolower(trim((string) $value));
        $value = str_replace([' ', '_', '-', '.', '/'], '', $value);

        return $value;
    }

    private function generatePassword(): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        return substr(str_shuffle(str_repeat($chars, 4)), 0, 8);
    }
}