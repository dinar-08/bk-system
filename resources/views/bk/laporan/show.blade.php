@extends('layouts.bk')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan')
@section('page-subtitle', 'Informasi lengkap laporan dan penanganan')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('bk.laporan.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">Detail Laporan</span>
    </div>

    {{-- session('success') sudah ditampilkan oleh layout, tidak perlu diulang di sini --}}

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SATU CARD BESAR, SETIAP SECTION DIPISAH GARIS --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm divide-y divide-slate-100">

        {{-- DATA SISWA + DETAIL LAPORAN (sejajar 2 kolom) --}}
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-7">

                {{-- Data Siswa --}}
                <div>
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                        <i data-feather="user" class="w-4 h-4 text-blue-600"></i>
                        <h2 class="text-sm font-bold text-slate-700">Data Siswa</h2>
                    </div>

                    <div class="w-32 h-32 mx-auto md:mx-0 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden mb-4">
                        @if(optional($laporan->siswa)->foto)
                            <img src="{{ route('foto.siswa', $laporan->siswa->id) }}"
                                class="w-full h-full object-cover" alt="Foto Siswa">
                        @else
                            <div class="text-center">
                                <i data-feather="user" class="w-8 h-8 text-slate-300 mx-auto mb-1"></i>
                                <p class="text-xs text-slate-400">Foto Siswa</p>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-2.5">
                        @foreach([
                            ['Nama', optional($laporan->siswa)->nama_siswa],
                            ['Kelas', optional($laporan->siswa)->kelas],
                            ['NIS', optional($laporan->siswa)->nis],
                            ['Orang Tua', optional($laporan->siswa)->nama_ortu],
                            ['No. WhatsApp', optional($laporan->siswa)->no_whatsapp],
                            ['Guru BK', optional($laporan->guruBk)->nama ?? 'Belum ditangani'],
                        ] as [$label, $val])
                            <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                                <p class="text-xs text-slate-400 mb-0.5">{{ $label }}</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $val ?? '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Detail Laporan --}}
                <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                    <i data-feather="file-text" class="w-4 h-4 text-blue-600"></i>
                    <h2 class="text-sm font-bold text-slate-700">Detail Laporan</h2>
                </div>

                @if($laporan->status === 'baru')
                    {{-- BELUM DIVERIFIKASI: kategori & jenis masalah jadi form --}}
                    <form action="{{ route('bk.laporan.update', $laporan->id) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="pemanggilan">

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-slate-400 mb-0.5">Judul</p>
                            <p class="text-sm font-semibold text-slate-700">{{ $laporan->judul_laporan ?? '-' }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                                <label for="kategori" class="block text-xs text-slate-400 mb-1.5">Kategori</label>
                                <select id="kategori" name="kategori" required
                                    class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <option value="">Pilih Kategori</option>
                                    @foreach(['akademik' => 'Akademik', 'sosial' => 'Sosial', 'perilaku' => 'Perilaku', 'emosional' => 'Emosional', 'lain-lain' => 'Lain-lain'] as $val => $lbl)
                                        <option value="{{ $val }}" {{ old('kategori', $laporan->kategori) == $val ? 'selected' : '' }}>
                                            {{ $lbl }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                                <label for="jenis_masalah" class="block text-xs text-slate-400 mb-1.5">Jenis Masalah</label>
                                <input type="text" id="jenis_masalah" name="jenis_masalah" required
                                    value="{{ old('jenis_masalah', $laporan->jenis_masalah) }}"
                                    placeholder="Contoh: Bolos, bertengkar, perundungan"
                                    class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-slate-400 mb-0.5">Lampiran</p>
                            @if($laporan->bukti)
                                <a href="{{ route('bukti.show', $laporan->id) }}" target="_blank"
                                    class="text-sm font-semibold text-blue-600 hover:underline inline-flex items-center gap-1">
                                    <i data-feather="paperclip" class="w-3 h-3"></i>
                                    Lihat Bukti
                                </a>
                            @else
                                <p class="text-sm text-slate-400">Tidak ada</p>
                            @endif
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-slate-400 mb-1">Deskripsi</p>
                            <p class="text-sm text-slate-700 leading-relaxed">{{ $laporan->deskripsi ?? '-' }}</p>
                        </div>

                        <div class="flex justify-end pt-1">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800">
                                <i data-feather="save" class="w-4 h-4"></i>
                                Simpan Verifikasi
                            </button>
                        </div>
                    </form>
                @else
                    {{-- SUDAH DIVERIFIKASI: tampil sebagai teks biasa --}}
                    <div class="space-y-3">
                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-slate-400 mb-0.5">Judul</p>
                            <p class="text-sm font-semibold text-slate-700">{{ $laporan->judul_laporan ?? '-' }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                                <p class="text-xs text-slate-400 mb-0.5">Kategori</p>
                                <p class="text-sm font-semibold text-slate-700">
                                    {{ $laporan->kategori ? ucfirst($laporan->kategori) : 'Belum ditentukan' }}
                                </p>
                            </div>

                            <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                                <p class="text-xs text-slate-400 mb-0.5">Jenis Masalah</p>
                                <p class="text-sm font-semibold text-slate-700">
                                    {{ $laporan->jenis_masalah ?? 'Belum ditentukan' }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-slate-400 mb-0.5">Lampiran</p>
                            @if($laporan->bukti)
                                <a href="{{ route('bukti.show', $laporan->id) }}" target="_blank"
                                    class="text-sm font-semibold text-blue-600 hover:underline inline-flex items-center gap-1">
                                    <i data-feather="paperclip" class="w-3 h-3"></i>
                                    Lihat Bukti
                                </a>
                            @else
                                <p class="text-sm text-slate-400">Tidak ada</p>
                            @endif
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                            <p class="text-xs text-slate-400 mb-1">Deskripsi</p>
                            <p class="text-sm text-slate-700 leading-relaxed">{{ $laporan->deskripsi ?? '-' }}</p>
                        </div>
                    </div>
                @endif
                </div>
            </div>
        </div>

        {{-- JADWAL PEMANGGILAN --}}
        @php
            // Ambil data pemanggilan paling terakhir (berdasarkan waktu dibuat)
            $pemanggilanTerakhir = $laporan->pemanggilan->sortByDesc('created_at')->first();

            // Perlu dijadwalkan ulang jika pemanggilan terakhir statusnya "Tidak Hadir"
            // dan tindak lanjutnya belum "selesai"
            $perluJadwalUlang = $pemanggilanTerakhir
                && $pemanggilanTerakhir->status_kehadiran === 'tidak_hadir'
                && $pemanggilanTerakhir->tindak_lanjut !== 'selesai';

            // Tampilkan form jika: belum ada pemanggilan sama sekali, ATAU perlu dijadwalkan ulang
            $tampilkanFormJadwal = $laporan->pemanggilan->isEmpty() || $perluJadwalUlang;
        @endphp

        @if($laporan->status === 'pemanggilan' && $tampilkanFormJadwal)
            <div class="p-6">
                <div class="flex items-center gap-2 mb-5 pb-4 border-b border-slate-100">
                    <i data-feather="phone" class="w-4 h-4 text-blue-600"></i>
                    <h2 class="text-sm font-bold text-slate-700">
                        {{ $laporan->pemanggilan->isEmpty() ? 'Jadwalkan Pemanggilan' : 'Jadwalkan Pemanggilan Ulang' }}
                    </h2>
                </div>

                @if($perluJadwalUlang)
                    <div class="mb-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-start gap-2">
                        <i data-feather="alert-triangle" class="w-4 h-4 text-amber-500 mt-0.5"></i>
                        <p class="text-sm text-amber-700">
                            Pemanggilan sebelumnya tidak dihadiri. Silakan jadwalkan pemanggilan ulang.
                        </p>
                    </div>
                @endif

                <form action="{{ route('bk.pemanggilan.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_pemanggilan" class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Pemanggilan</label>
                            <input type="date" id="tanggal_pemanggilan" name="tanggal_pemanggilan" required
                                value="{{ old('tanggal_pemanggilan') }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                        <div>
                            <label for="waktu_pemanggilan" class="block text-xs font-semibold text-slate-600 mb-1.5">Waktu Pemanggilan</label>
                            <input type="time" id="waktu_pemanggilan" name="waktu_pemanggilan" required
                                value="{{ old('waktu_pemanggilan') }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                        <div>
                            <label for="pihak_dipanggil" class="block text-xs font-semibold text-slate-600 mb-1.5">Pihak yang Dipanggil</label>
                            <select id="pihak_dipanggil" name="pihak_dipanggil" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="siswa">Siswa</option>
                                <option value="orang_tua">Orang Tua / Wali</option>
                                <option value="siswa_orang_tua">Siswa dan Orang Tua / Wali</option>
                            </select>
                        </div>

                        <div>
                            <label for="tujuan" class="block text-xs font-semibold text-slate-600 mb-1.5">Tujuan</label>
                            <input type="text" id="tujuan" name="tujuan" required
                                value="{{ old('tujuan') }}"
                                placeholder="Tujuan pemanggilan"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                    </div>

                    <div class="flex justify-end mt-5">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800">
                            <i data-feather="calendar" class="w-4 h-4"></i>
                            {{ $laporan->pemanggilan->isEmpty() ? 'Simpan Jadwal Pemanggilan' : 'Simpan Jadwal Pemanggilan Ulang' }}
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- RIWAYAT PEMANGGILAN --}}
        <div class="p-6">
            <div class="flex items-center gap-2 mb-5 pb-4 border-b border-slate-100">
                <i data-feather="list" class="w-4 h-4 text-blue-600"></i>
                <h2 class="text-sm font-bold text-slate-700">Riwayat Pemanggilan</h2>
            </div>

            <div class="space-y-4">
                @forelse($laporan->pemanggilan->sortByDesc('created_at') as $index => $item)
                    <div class="border border-slate-200 bg-slate-50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                Pemanggilan ke-{{ $laporan->pemanggilan->count() - $index }}
                            </span>
                            @if($item->status_kehadiran === 'tidak_hadir')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-600 text-xs font-semibold">
                                    <i data-feather="x-circle" class="w-3 h-3"></i>
                                    Tidak Hadir
                                </span>
                            @elseif($item->status_kehadiran === 'hadir')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-50 text-green-600 text-xs font-semibold">
                                    <i data-feather="check-circle" class="w-3 h-3"></i>
                                    Hadir
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">
                                    <i data-feather="clock" class="w-3 h-3"></i>
                                    Belum
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                            @foreach([
                                ['Tanggal', $item->tanggal_pemanggilan],
                                ['Waktu', $item->waktu_pemanggilan],
                                ['Pihak', str_replace('_', ' ', ucfirst($item->pihak_dipanggil))],
                                ['Status', str_replace('_', ' ', ucfirst($item->status_kehadiran))]
                            ] as [$lbl, $val])
                                <div>
                                    <p class="text-xs text-slate-400 mb-0.5">{{ $lbl }}</p>
                                    <p class="text-sm font-semibold text-slate-700">{{ $val }}</p>
                                </div>
                            @endforeach
                        </div>

                        @if($item->tujuan)
                            <p class="text-sm text-slate-600 mb-4">{{ $item->tujuan }}</p>
                        @endif

                        @if($item->status_kehadiran === 'belum')
                            <form action="{{ route('bk.pemanggilan.update', $item->id) }}" method="POST"
                                class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label for="status_kehadiran_{{ $item->id }}" class="block text-xs text-slate-400 mb-1">Status Kehadiran</label>
                                    <select id="status_kehadiran_{{ $item->id }}" name="status_kehadiran"
                                        class="w-full rounded-xl border-slate-200 text-sm px-3 py-2 focus:ring-blue-400">
                                        <option value="belum" {{ $item->status_kehadiran == 'belum' ? 'selected' : '' }}>Belum</option>
                                        <option value="hadir" {{ $item->status_kehadiran == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                        <option value="tidak_hadir" {{ $item->status_kehadiran == 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="tindak_lanjut_{{ $item->id }}" class="block text-xs text-slate-400 mb-1">Tindak Lanjut</label>
                                    <select id="tindak_lanjut_{{ $item->id }}" name="tindak_lanjut"
                                        class="w-full rounded-xl border-slate-200 text-sm px-3 py-2 focus:ring-blue-400">
                                        <option value="belum" {{ $item->tindak_lanjut == 'belum' ? 'selected' : '' }}>Belum</option>
                                        <option value="monitoring" {{ $item->tindak_lanjut == 'monitoring' ? 'selected' : '' }}>Lanjut Monitoring</option>
                                        <option value="selesai" {{ $item->tindak_lanjut == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="tanggal_monitoring_{{ $item->id }}" class="block text-xs text-slate-400 mb-1">Tanggal Monitoring</label>
                                    <input type="date" id="tanggal_monitoring_{{ $item->id }}" name="tanggal_monitoring"
                                        value="{{ old('tanggal_monitoring', $item->tanggal_monitoring) }}"
                                        class="w-full rounded-xl border-slate-200 text-sm px-3 py-2 focus:ring-blue-400">
                                </div>

                                <div>
                                    <label for="catatan_{{ $item->id }}" class="block text-xs text-slate-400 mb-1">Catatan</label>
                                    <input type="text" id="catatan_{{ $item->id }}" name="catatan"
                                        value="{{ old('catatan', $item->catatan) }}"
                                        placeholder="Catatan"
                                        class="w-full rounded-xl border-slate-200 text-sm px-3 py-2 focus:ring-blue-400">
                                </div>

                                @php
                                    $nomorWa = preg_replace('/[^0-9]/', '', optional($laporan->siswa)->no_whatsapp ?? '');

                                    if (str_starts_with($nomorWa, '0')) {
                                        $nomorWa = '62' . substr($nomorWa, 1);
                                    }

                                    $namaOrtu = optional($laporan->siswa)->nama_ortu ?? 'Orang Tua/Wali';
                                    $namaSiswa = optional($laporan->siswa)->nama_siswa ?? '-';
                                    $namaGuruBk = optional($laporan->guruBk)->nama ?? 'Guru BK';

                                    \Carbon\Carbon::setLocale('id');
                                    $tgl = \Carbon\Carbon::parse($item->tanggal_pemanggilan)
                                        ->isoFormat('dddd, D MMMM YYYY');
                                    $wkt = \Carbon\Carbon::parse($item->waktu_pemanggilan)->format('H:i') . ' WIB';

                                    $pesan = "Assalamualaikum Bapak/Ibu {$namaOrtu},\n\n"
                                        . "Kami dari Guru BK SDIT Al-Kautsar mengundang Bapak/Ibu untuk hadir ke sekolah terkait ananda *{$namaSiswa}*.\n\n"
                                        . "Tanggal: {$tgl}\n"
                                        . "Waktu: {$wkt}\n"
                                        . "Tujuan: {$item->tujuan}\n"
                                        . "Guru BK: {$namaGuruBk}\n\n"
                                        . "Mohon hadir tepat waktu. Terima kasih.";
                                @endphp

                                <div class="md:col-span-4 flex flex-wrap justify-end gap-3">
                                    @if($nomorWa)
                                        <a href="https://wa.me/{{ $nomorWa }}?text={{ urlencode($pesan) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition-colors">
                                            <i data-feather="message-circle" class="w-4 h-4"></i>
                                            Kirim WA
                                        </a>
                                    @endif

                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-sm font-semibold">
                                        <i data-feather="save" class="w-4 h-4"></i>
                                        Simpan Hasil Pemanggilan
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-8 text-center">
                        <i data-feather="calendar" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-400">Belum ada jadwal pemanggilan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection