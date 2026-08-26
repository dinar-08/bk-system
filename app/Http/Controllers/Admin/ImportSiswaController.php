<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeUpdate;
use App\Models\Siswa;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportSiswaController extends Controller
{
    /**
     * Nama sheet yang dilewati karena bukan data siswa per-kelas
     * (mis. sheet rekap/gabungan yang isinya beberapa kelas berjajar,
     * atau sheet referensi seperti dropdown kategori).
     */
    private const SHEET_DILEWATI = ['master', 'rekap', 'rekapitulasi', 'helper', 'subcategory'];

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        $path = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($path);

        $berhasil = [];
        $gagal = [];
        $adaHeaderDitemukan = false;

        $periodeAktif = PeriodeUpdate::aktifSekarang();
        $tahunAjaranDefault = $periodeAktif
            ? $periodeAktif->tahun_ajaran
            : now()->year . '/' . (now()->year + 1);

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $namaSheet = trim($sheet->getTitle());

            if (in_array(strtolower($namaSheet), self::SHEET_DILEWATI, true)) {
                continue;
            }

            $rows = $sheet->toArray(null, true, true, true);

            if (count($rows) < 2) {
                continue;
            }

            $header = null;
            $barisHeader = null;
            $kolom = null;

            foreach ($rows as $index => $row) {
                $hasilDeteksi = $this->deteksiKolom($row);

                // Wajib ada NIS dan Nama. Kolom "kelas" opsional di level sheet
                // (kalau tidak ada, nama sheet dipakai sebagai kelas, lihat di bawah).
                if ($hasilDeteksi['nis'] !== null && $hasilDeteksi['nama'] !== null) {
                    $header = $row;
                    $kolom = $hasilDeteksi;
                    $barisHeader = $index;
                    break;
                }
            }

            // Sheet ini tidak punya header NIS+Nama yang valid -> lewati, bukan error fatal,
            // supaya sheet lain (mis. sheet referensi/dropdown) tidak menggagalkan seluruh import.
            if ($header === null) {
                continue;
            }

            $adaHeaderDitemukan = true;

            $dataRows = array_slice($rows, $barisHeader);
            array_shift($dataRows);

            // Kalau sheet tidak punya kolom "kelas" sendiri, pakai nama sheet sebagai kelas
            // (format file per-kelas: satu sheet = satu kelas, contoh sheet "1A", "1B", dst).
            $kelasDariNamaSheet = $kolom['kelas'] === null ? $namaSheet : null;

            foreach ($dataRows as $no => $row) {
                $rowNum = $no + 2;

                $nis = trim((string) ($row[$kolom['nis']] ?? ''));
                $nama = trim((string) ($row[$kolom['nama']] ?? ''));

                $kelas = $kolom['kelas'] !== null
                    ? trim((string) ($row[$kolom['kelas']] ?? ''))
                    : $kelasDariNamaSheet;

                $jenisKelaminMentah = $kolom['jenis_kelamin'] !== null
                    ? trim((string) ($row[$kolom['jenis_kelamin']] ?? ''))
                    : '';

                $tanggalLahirMentah = $kolom['tanggal_lahir'] !== null
                    ? ($row[$kolom['tanggal_lahir']] ?? '')
                    : '';

                $tahunAjaranMentah = $kolom['tahun_ajaran'] !== null
                    ? trim((string) ($row[$kolom['tahun_ajaran']] ?? ''))
                    : '';

                if ($nis === '' && $nama === '') {
                    continue;
                }

                if (empty($nis) || empty($nama) || empty($kelas)) {
                    $gagal[] = [
                        'sheet' => $namaSheet,
                        'baris' => $rowNum,
                        'nis' => $nis ?: '-',
                        'nama' => $nama ?: '-',
                        'alasan' => 'Kolom NIS, Nama, atau Kelas kosong',
                    ];
                    continue;
                }

                if (User::where('username', $nis)->exists() || Siswa::where('nis', $nis)->exists()) {
                    $gagal[] = [
                        'sheet' => $namaSheet,
                        'baris' => $rowNum,
                        'nis' => $nis,
                        'nama' => $nama,
                        'alasan' => 'NIS sudah terdaftar',
                    ];
                    continue;
                }

                $jenisKelamin = $this->normalisasiJenisKelamin($jenisKelaminMentah);
                $tanggalLahir = $this->parseTanggalLahir($tanggalLahirMentah);
                $tahunAjaran = $tahunAjaranMentah !== '' ? $tahunAjaranMentah : $tahunAjaranDefault;

                try {
                    DB::transaction(function () use ($nis, $nama, $kelas, $jenisKelamin, $tanggalLahir, $tahunAjaran, &$berhasil) {
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
                            'jenis_kelamin' => $jenisKelamin,
                            'tanggal_lahir' => $tanggalLahir,
                            'tahun_ajaran' => $tahunAjaran,
                        ]);

                        $berhasil[] = [
                            'nis' => $nis,
                            'nama' => $nama,
                            'kelas' => $kelas,
                            'jenis_kelamin' => $jenisKelamin === 'L' ? 'Laki-Laki' : ($jenisKelamin === 'P' ? 'Perempuan' : '-'),
                            'tanggal_lahir' => $tanggalLahir ? $tanggalLahir->format('d-m-Y') : '-',
                            'tahun_ajaran' => $tahunAjaran,
                            'password' => $password,
                        ];
                    });
                } catch (\Exception $e) {
                    $gagal[] = [
                        'sheet' => $namaSheet,
                        'baris' => $rowNum,
                        'nis' => $nis,
                        'nama' => $nama,
                        'alasan' => 'Error sistem: ' . $e->getMessage(),
                    ];
                }
            }
        }

        if (!$adaHeaderDitemukan) {
            return back()->withErrors([
                'file' => 'Header Excel tidak ditemukan di sheet manapun. Wajib ada kolom NIS/Identity Code dan Nama Siswa.',
            ]);
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

    /**
     * Deteksi kolom NIS, Nama, Kelas, Jenis Kelamin, Tanggal Lahir, Tahun Ajaran
     * dari satu baris header. Mendukung berbagai alias nama kolom dari format
     * file yang berbeda-beda (template SIPGN, file rekap per-kelas, dll).
     */
    private function deteksiKolom(array $header): array
    {
        $kolom = [
            'nis' => null,
            'nama' => null,
            'kelas' => null,
            'jenis_kelamin' => null,
            'tanggal_lahir' => null,
            'tahun_ajaran' => null,
        ];

        foreach ($header as $key => $value) {
            $namaHeader = $this->normalisasiHeader($value);

            // NIS / identity_code / identitas_code
            if (in_array($namaHeader, [
                'nis', 'nomorindaksiswa', 'nomorinduksiswa', 'nomorinduk',
                'identitycode', 'identitascode', 'identitycode', 'kodeidentitas',
            ])) {
                $kolom['nis'] = $key;
            }

            // Nama
            if (in_array($namaHeader, [
                'nama', 'namasiswa', 'namalengkap', 'namalengkapsiswa', 'fullname',
            ])) {
                $kolom['nama'] = $key;
            }

            // Kelas / sub_kategori
            if (in_array($namaHeader, [
                'kelas', 'rumbel', 'rombonganbelajar', 'subkategori', 'subcategory',
            ])) {
                $kolom['kelas'] = $key;
            }

            // Jenis kelamin / gender
            if (in_array($namaHeader, ['jeniskelamin', 'jk', 'gender', 'sex'])) {
                $kolom['jenis_kelamin'] = $key;
            }

            // Tanggal lahir / date_of_birth
            if (in_array($namaHeader, [
                'tanggallahir', 'tgllahir', 'tanggalahir', 'birthdate', 'dateofbirth', 'ttl',
            ])) {
                $kolom['tanggal_lahir'] = $key;
            }

            // Tahun ajaran
            if (in_array($namaHeader, ['tahunajaran', 'tahunpelajaran', 'ta', 'tapel'])) {
                $kolom['tahun_ajaran'] = $key;
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

    private function normalisasiJenisKelamin(string $value): ?string
    {
        $value = strtolower(trim($value));
        $value = str_replace([' ', '-', '_', '.'], '', $value);

        if ($value === '') {
            return null;
        }

        // str_starts_with menangani typo seperti "Laki-Lak" / "Laki2" / "Lakilaki"
        if (str_starts_with($value, 'laki') || in_array($value, ['l', 'male', 'm'])) {
            return 'L';
        }

        if (str_starts_with($value, 'perempuan') || in_array($value, ['p', 'female', 'f', 'wanita'])) {
            return 'P';
        }

        return null;
    }

    private function parseTanggalLahir($value): ?Carbon
    {
        if ($value === '' || $value === null) {
            return null;
        }

        // Kalau Excel simpan sebagai serial number (cell diformat sebagai Date asli)
        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value));
            } catch (\Exception $e) {
                return null;
            }
        }

        $value = trim((string) $value);
        $value = ltrim($value, "'"); // beberapa file menyimpan tanggal sbg text dgn prefix ' (mis. '11/01/2020)

        $format = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y', 'd-m-y'];

        foreach ($format as $f) {
            try {
                return Carbon::createFromFormat($f, $value);
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    private function generatePassword(): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        return substr(str_shuffle(str_repeat($chars, 4)), 0, 8);
    }
}