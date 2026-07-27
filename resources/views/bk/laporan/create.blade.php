@extends('layouts.bk')
@section('title', 'Buat Laporan')
@section('page-title', 'Buat Laporan')
@section('page-subtitle', 'Tambahkan laporan permasalahan siswa baru')
@section('content')

    <div class="mb-6 flex items-center gap-2 sm:gap-3 flex-wrap">
        <a href="{{ route('bk.laporan.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">Buat Laporan</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
        <div class="mb-5 pb-5 border-b border-slate-100">
            <h2 class="font-bold text-slate-800">Formulir Laporan</h2>
            <p class="text-sm text-slate-500 mt-0.5">Isi semua data dengan lengkap dan benar.</p>
        </div>

        <form action="{{ route('bk.laporan.store') }}" method="POST" enctype="multipart/form-data" id="formLaporan">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">

                {{-- Cari Siswa --}}
                <div class="relative">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Siswa</label>

                    <input type="text" id="nama_siswa" autocomplete="off" placeholder="contoh: Dinar"
                        value="{{ old('nama_siswa') }}"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-base sm:text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400">

                    <input type="hidden" name="nis" id="nis" value="{{ old('nis') }}">

                    {{-- Dropdown autocomplete: menyatu langsung dengan input, tanpa celah --}}
                    <div id="hasil_siswa"
                        class="hidden absolute left-0 right-0 top-full -mt-px z-20 bg-white rounded-b-xl border border-t-0 border-slate-200 shadow-lg overflow-hidden">
                    </div>

                    @error('nis')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kelas --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kelas</label>

                    <select id="kelas_siswa"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-base sm:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Pilih kelas</option>
                        @foreach($siswa->pluck('kelas')->unique()->sort() as $k)
                            <option value="{{ $k }}" {{ old('kelas_siswa') == $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Kategori
                    </label>

                    <select name="kategori" id="kategori" required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-base sm:text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('kategori') border-red-300 @enderror">

                        <option value="">Pilih kategori</option>

                        @foreach ($kategori as $kat)
                            <option value="{{ $kat }}" {{ old('kategori') == $kat ? 'selected' : '' }}>
                                {{ ucfirst($kat) }}
                            </option>
                        @endforeach

                    </select>

                    @error('kategori')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Jenis Masalah --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Masalah</label>
                    <input type="text" name="jenis_masalah" value="{{ old('jenis_masalah') }}"
                        placeholder="Contoh: Bolos, bertengkar, terlambat"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-base sm:text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('jenis_masalah') border-red-300 @enderror">
                    @error('jenis_masalah')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Judul --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Judul Laporan</label>
                    <input type="text" name="judul_laporan" value="{{ old('judul_laporan') }}"
                        placeholder="Contoh: Laporan Perilaku Siswa"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-base sm:text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('judul_laporan') border-red-300 @enderror">
                    @error('judul_laporan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Deskripsi --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Laporan</label>
                    <textarea name="deskripsi" rows="5"
                        placeholder="Tuliskan deskripsi permasalahan siswa secara lengkap..."
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-base sm:text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none @error('deskripsi') border-red-300 @enderror">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Upload Bukti: foto, pdf, rekaman audio/video --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bukti / Lampiran</label>
                    <label
                        class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 cursor-pointer bg-slate-50 hover:bg-blue-50 hover:border-blue-200 transition-colors">
                        <i data-feather="paperclip" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                        <span id="fileName" class="text-sm text-slate-400 truncate">Foto, PDF, atau rekaman
                            audio/video</span>
                        <input type="file" name="bukti" id="buktiInput" class="hidden"
                            accept="image/*,.pdf,audio/*,video/*,.mp3,.mp4,.mov,.wav,.m4a,.ogg"
                            onchange="previewBukti(this)">
                    </label>
                    <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG, PDF, MP3, MP4, MOV, WAV · Maks 50MB</p>
                    <div id="previewWrapper" class="hidden mt-3 rounded-xl overflow-hidden border border-slate-200">
                        <img id="previewImg" src="" alt="Preview" class="w-full object-cover max-h-56">
                    </div>
                    @error('bukti')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div
                class="mt-6 pt-5 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3
                                            sticky bottom-0 -mx-4 sm:mx-0 px-4 sm:px-0 pb-4 sm:pb-0 bg-white sm:bg-transparent sm:static">
                <a href="{{ route('bk.laporan.index') }}"
                    class="px-5 py-3 sm:py-2.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 active:bg-slate-100 transition-colors text-center w-full sm:w-auto">Batal</a>
                <button type="submit"
                    class="px-5 py-3 sm:py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 active:bg-blue-900 transition-colors w-full sm:w-auto">Simpan
                    Laporan</button>
            </div>
        </form>
    </div>

    <script>
        const siswaData = @json($siswa);
        const namaInput = document.getElementById('nama_siswa');
        const kelasSelect = document.getElementById('kelas_siswa');
        const siswaIdInput = document.getElementById('nis');
        const hasilSiswa = document.getElementById('hasil_siswa');
        const formLaporan = document.getElementById('formLaporan');

        function tutupDropdown() {
            hasilSiswa.innerHTML = '';
            hasilSiswa.classList.add('hidden');
            namaInput.classList.remove('rounded-b-none');
        }

        function bukaDropdown() {
            hasilSiswa.classList.remove('hidden');
            namaInput.classList.add('rounded-b-none');
        }

        function pilihSiswa(siswa) {
            namaInput.value = siswa.nama_siswa;
            siswaIdInput.value = siswa.nis;

            // Jika kelas sebelumnya kosong, isi otomatis sesuai data siswa yang dipilih.
            // Jika kelas sudah dipilih sebelumnya, biarkan (hasil pencarian memang sudah difilter ke kelas itu).
            if (!kelasSelect.value) {
                kelasSelect.value = siswa.kelas;
            }

            tutupDropdown();
        }

        // Pencarian bisa dimulai dari field mana saja (nama atau kelas), tanpa urutan tertentu.
        function cariSiswa() {
            const nama = namaInput.value.toLowerCase().trim();
            const kelas = kelasSelect.value;

            siswaIdInput.value = '';
            tutupDropdown();

            // nama belum diketik / terlalu pendek -> diam saja
            if (nama.length < 2) return;

            let hasil;

            if (kelas === '') {
                // Skenario 2: kelas belum dipilih -> cari di seluruh siswa/kelas
                hasil = siswaData.filter(item =>
                    item.nama_siswa.toLowerCase().includes(nama)
                );
            } else {
                // Skenario 1: kelas sudah dipilih -> cari hanya di kelas tersebut
                hasil = siswaData.filter(item =>
                    item.kelas === kelas &&
                    item.nama_siswa.toLowerCase().includes(nama)
                );
            }

            if (hasil.length === 0) {
                hasilSiswa.innerHTML = `
                        <div class="px-4 py-3 text-xs text-red-500">
                            ${kelas === '' ? 'Siswa tidak ditemukan.' : 'Siswa tidak ditemukan di kelas ini.'}
                        </div>`;
                bukaDropdown();
                return;
            }

            // cocok ke 1 siswa saja -> nama, NIS, dan kelas otomatis terisi, dropdown tidak perlu tampil
            if (hasil.length === 1) {
                pilihSiswa(hasil[0]);
                return;
            }

            // ada beberapa hasil -> tampilkan sebagai satu dropdown menyatu (mirip Google), baris dipisah garis tipis
            hasil.forEach((siswa, idx) => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'w-full flex items-center justify-between gap-3 px-4 py-2.5 text-left text-sm hover:bg-blue-50 active:bg-blue-100 transition-colors'
                    + (idx !== hasil.length - 1 ? ' border-b border-slate-100' : '');

                // NIS tidak ditampilkan di layar, tapi tetap tersimpan lewat objek siswa saat dipilih.
                // Kelas tetap ditampilkan sebagai pembeda hanya kalau pencarian lintas kelas (kelas belum dipilih).
                const identitas = kelas === '' ? `Kelas ${siswa.kelas}` : '';

                item.innerHTML = `
                                        <span class="font-medium text-slate-700 truncate">${siswa.nama_siswa}</span>
                                        ${identitas ? `<span class="text-xs text-slate-400 flex-shrink-0">${identitas}</span>` : ''}
                                    `;

                item.onclick = function () {
                    pilihSiswa(siswa);
                };
                hasilSiswa.appendChild(item);
            });

            bukaDropdown();
        }

        function previewBukti(input) {
            const file = input.files[0];

            if (file) {
                document.getElementById('fileName').textContent = '✓ ' + file.name;

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();

                    reader.onload = e => {
                        document.getElementById('previewImg').src = e.target.result;
                        document.getElementById('previewWrapper').classList.remove('hidden');
                    };

                    reader.readAsDataURL(file);
                } else {
                    document.getElementById('previewWrapper').classList.add('hidden');
                }
            }
        }

        // pencarian dipicu baik saat mengetik nama maupun saat kelas berubah
        namaInput.addEventListener('input', cariSiswa);
        kelasSelect.addEventListener('change', cariSiswa);

        // tutup dropdown kalau user klik di luar area input/dropdown
        document.addEventListener('click', function (e) {
            if (!namaInput.contains(e.target) && !hasilSiswa.contains(e.target)) {
                tutupDropdown();
            }
        });

        // cegah submit kalau siswa belum benar-benar dipilih dari daftar
        formLaporan.addEventListener('submit', function (e) {
            if (!siswaIdInput.value) {
                e.preventDefault();
                hasilSiswa.innerHTML = `<div class="px-4 py-3 text-xs text-red-500">Silakan pilih siswa dari daftar terlebih dahulu.</div>`;
                bukaDropdown();
                namaInput.focus();
            }
        });
    </script>

@endsection