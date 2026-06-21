@extends('layouts.bk')
@section('title', 'Buat Laporan')
@section('page-title', 'Buat Laporan')
@section('page-subtitle', 'Tambahkan laporan permasalahan siswa baru')
@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('bk.laporan.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">Buat Laporan</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="mb-5 pb-5 border-b border-slate-100">
            <h2 class="font-bold text-slate-800">Formulir Laporan</h2>
            <p class="text-sm text-slate-500 mt-0.5">Isi semua data dengan lengkap dan benar.</p>
        </div>

        <form action="{{ route('bk.laporan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Cari Siswa --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Siswa</label>

                    <input type="text" id="nama_siswa" autocomplete="off" placeholder="contoh: Dinar"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400">

                    <input type="hidden" name="siswa_id" id="siswa_id" value="{{ old('siswa_id') }}">

                    <div id="hasil_siswa" class="mt-2 space-y-2"></div>

                    @error('siswa_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kelas --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kelas</label>

                    <select id="kelas_siswa"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Pilih kelas</option>
                        @foreach($siswa->pluck('kelas')->unique()->sort() as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Kategori
                    </label>

                    <select name="kategori" id="kategori" required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('kategori') border-red-300 @enderror">

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
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('jenis_masalah') border-red-300 @enderror">
                    @error('jenis_masalah')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Judul Laporan</label>
                    <input type="text" name="judul_laporan" value="{{ old('judul_laporan') }}"
                        placeholder="Contoh: Laporan Perilaku Siswa"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('judul_laporan') border-red-300 @enderror">
                    @error('judul_laporan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Deskripsi --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Laporan</label>
                    <textarea name="deskripsi" rows="5"
                        placeholder="Tuliskan deskripsi permasalahan siswa secara lengkap..."
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none @error('deskripsi') border-red-300 @enderror">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Upload Bukti: foto, pdf, rekaman audio/video --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bukti / Lampiran</label>
                    <label
                        class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 cursor-pointer bg-slate-50 hover:bg-blue-50 hover:border-blue-200 transition-colors">
                        <i data-feather="paperclip" class="w-4 h-4 text-slate-400"></i>
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

            <div class="mt-6 pt-5 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('bk.laporan.index') }}"
                    class="px-5 py-2.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit"
                    class="px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors">Simpan
                    Laporan</button>
            </div>
        </form>
    </div>

    <script>
        const siswaData = @json($siswa);
        const namaInput = document.getElementById('nama_siswa');
        const kelasSelect = document.getElementById('kelas_siswa');
        const siswaIdInput = document.getElementById('siswa_id');
        const hasilSiswa = document.getElementById('hasil_siswa');

        function cariSiswa() {
            const nama = namaInput.value.toLowerCase().trim();
            const kelas = kelasSelect.value;

            siswaIdInput.value = '';
            hasilSiswa.innerHTML = '';

            if (nama.length < 2 || kelas === '') return;

            const hasil = siswaData.filter(item =>
                item.kelas === kelas &&
                item.nama_siswa.toLowerCase().includes(nama)
            );

            if (hasil.length === 0) {
                hasilSiswa.innerHTML = `<p class="text-xs text-red-500">Siswa tidak ditemukan.</p>`;
                return;
            }
            if (hasil.length === 1) {
                const siswa = hasil[0];

                namaInput.value = siswa.nama_siswa;
                siswaIdInput.value = siswa.id;

                hasilSiswa.innerHTML = '';

                return;
            }

            hasil.forEach(siswa => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'w-full text-left px-4 py-3 rounded-xl border border-slate-200 hover:bg-blue-50 text-sm';

                item.innerHTML = `
            <p class="font-semibold text-slate-700">${siswa.nama_siswa}</p>
        `;

                item.onclick = function () {
                    namaInput.value = siswa.nama_siswa;
                    siswaIdInput.value = siswa.id;

                    hasilSiswa.innerHTML = '';
                };
                hasilSiswa.appendChild(item);
            });
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

        namaInput.addEventListener('input', cariSiswa);
        kelasSelect.addEventListener('change', cariSiswa);
    </script>

@endsection