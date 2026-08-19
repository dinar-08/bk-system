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

        <form action="{{ route('bk.laporan.store') }}" method="POST" enctype="multipart/form-data" id="formLaporan"
            autocomplete="off">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">

                <div class="relative">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Siswa</label>

                    <div id="nama_siswa" contenteditable="true" spellcheck="false" data-placeholder="contoh: Dinar"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-base sm:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400 empty:before:content-[attr(data-placeholder)] empty:before:text-slate-400 whitespace-pre-wrap break-words">{{ old('nama_siswa') }}</div>

                    <input type="hidden" name="nis" id="nis" value="{{ old('nis') }}">

                    <div id="hasil_siswa"
                        class="hidden absolute left-0 right-0 top-full -mt-px z-20 bg-white rounded-b-xl border border-t-0 border-slate-200 shadow-lg overflow-hidden">
                    </div>

                    @error('nis')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

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

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Masalah</label>
                    <input type="text" name="jenis_masalah" value="{{ old('jenis_masalah') }}"
                        placeholder="Contoh: Bolos, bertengkar, terlambat"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-base sm:text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('jenis_masalah') border-red-300 @enderror">
                    @error('jenis_masalah')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Judul Laporan</label>
                    <input type="text" name="judul_laporan" value="{{ old('judul_laporan') }}"
                        placeholder="Contoh: Laporan Perilaku Siswa"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-base sm:text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('judul_laporan') border-red-300 @enderror">
                    @error('judul_laporan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Laporan</label>
                    <textarea name="deskripsi" rows="5"
                        placeholder="Tuliskan deskripsi permasalahan siswa secara lengkap..."
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-base sm:text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none @error('deskripsi') border-red-300 @enderror">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

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

                    {{-- Preview Gambar --}}
                    <div id="previewWrapperImg" class="hidden mt-3 rounded-xl overflow-hidden border border-slate-200">
                        <img id="previewImg" src="" alt="Preview" class="w-full object-contain max-h-[500px] bg-slate-100">
                    </div>

                    {{-- Preview Video --}}
                    <div id="previewWrapperVideo"
                        class="hidden mt-3 rounded-xl overflow-hidden border border-slate-200 bg-black max-h-[500px] flex justify-center">
                        <video id="previewVideo" controls class="h-full max-h-[500px] w-auto object-cover"></video>
                    </div>

                    {{-- Preview Audio --}}
                    <div id="previewWrapperAudio" class="hidden mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3.5">
                        <audio id="previewAudio" controls class="w-full"></audio>
                    </div>

                    {{-- Preview PDF --}}
                    <div id="previewWrapperPdf" class="hidden mt-3 rounded-xl overflow-hidden border border-slate-200">
                        <iframe id="previewPdf" src="" class="w-full h-[500px]"></iframe>
                    </div>

                    @error('bukti')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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

        // Bersihkan whitespace bawaan Blade/editor supaya placeholder muncul saat kosong
        if (!namaInput.textContent.trim()) {
            namaInput.textContent = '';
        }

        function getNamaValue() {
            return namaInput.textContent;
        }

        function setNamaValue(value) {
            namaInput.textContent = value;

            const range = document.createRange();
            const sel = window.getSelection();
            range.selectNodeContents(namaInput);
            range.collapse(false);
            sel.removeAllRanges();
            sel.addRange(range);
        }

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
            setNamaValue(siswa.nama_siswa);
            siswaIdInput.value = siswa.nis;

            if (!kelasSelect.value) {
                kelasSelect.value = siswa.kelas;
            }

            tutupDropdown();
        }

        function cariSiswa() {
            const nama = getNamaValue().toLowerCase().trim();
            const kelas = kelasSelect.value;

            siswaIdInput.value = '';
            tutupDropdown();

            if (nama.length < 2) return;

            let hasil;

            if (kelas === '') {

                hasil = siswaData.filter(item =>
                    item.nama_siswa.toLowerCase().includes(nama)
                );
            } else {

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

            if (kelas !== '' && hasil.length === 1) {
                pilihSiswa(hasil[0]);
                return;
            }

            hasil.forEach((siswa, idx) => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'w-full flex items-center justify-between gap-3 px-4 py-2.5 text-left text-sm hover:bg-blue-50 active:bg-blue-100 transition-colors'
                    + (idx !== hasil.length - 1 ? ' border-b border-slate-100' : '');

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

            const wrappers = {
                img: document.getElementById('previewWrapperImg'),
                video: document.getElementById('previewWrapperVideo'),
                audio: document.getElementById('previewWrapperAudio'),
                pdf: document.getElementById('previewWrapperPdf'),
            };

            const hideAll = () => {
                wrappers.img.classList.add('hidden');
                wrappers.video.classList.add('hidden');
                wrappers.audio.classList.add('hidden');
                wrappers.pdf.classList.add('hidden');

                document.getElementById('previewVideo').src = '';
                document.getElementById('previewAudio').src = '';
                document.getElementById('previewPdf').src = '';
            };

            if (!file) {
                hideAll();
                return;
            }

            document.getElementById('fileName').textContent = '✓ ' + file.name;
            hideAll();

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('previewImg').src = e.target.result;
                    wrappers.img.classList.remove('hidden');
                };
                reader.readAsDataURL(file);

            } else if (file.type.startsWith('video/')) {
                const url = URL.createObjectURL(file);
                document.getElementById('previewVideo').src = url;
                wrappers.video.classList.remove('hidden');

            } else if (file.type.startsWith('audio/')) {
                const url = URL.createObjectURL(file);
                document.getElementById('previewAudio').src = url;
                wrappers.audio.classList.remove('hidden');

            } else if (file.type === 'application/pdf') {
                const url = URL.createObjectURL(file);
                document.getElementById('previewPdf').src = url;
                wrappers.pdf.classList.remove('hidden');
            }
        }

        namaInput.addEventListener('input', cariSiswa);
        kelasSelect.addEventListener('change', cariSiswa);

        namaInput.addEventListener('paste', function (e) {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text/plain');
            document.execCommand('insertText', false, text);
        });

        namaInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        document.addEventListener('click', function (e) {
            if (!namaInput.contains(e.target) && !hasilSiswa.contains(e.target)) {
                tutupDropdown();
            }
        });

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