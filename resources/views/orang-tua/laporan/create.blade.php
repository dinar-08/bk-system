@extends('layouts.orang-tua')

@section('title', 'Buat Laporan')
@section('page-title', 'Buat Laporan')
@section('page-subtitle', 'Laporkan permasalahan anak kepada Guru BK')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('orang_tua.laporan.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">Buat Laporan</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

        <div class="mb-5 pb-5 border-b border-slate-100">
            <h2 class="font-bold text-slate-800">Formulir Laporan Orang Tua</h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Laporkan Permasalahan Anak Anda.
            </p>
        </div>

        {{-- Data Siswa --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="rounded-xl bg-blue-50 border border-blue-100 p-4">
                <p class="text-xs text-slate-500 mb-1">Nama Siswa</p>
                <p class="text-sm font-bold text-slate-800">{{ $siswa->nama_siswa }}</p>
            </div>

            <div class="rounded-xl bg-blue-50 border border-blue-100 p-4">
                <p class="text-xs text-slate-500 mb-1">NIS</p>
                <p class="text-sm font-bold text-slate-800">{{ $siswa->nis }}</p>
            </div>

            <div class="rounded-xl bg-blue-50 border border-blue-100 p-4">
                <p class="text-xs text-slate-500 mb-1">Kelas</p>
                <p class="text-sm font-bold text-slate-800">{{ $siswa->kelas }}</p>
            </div>
        </div>

        <form action="{{ route('orang_tua.laporan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-5">

                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Judul Laporan</label>

                    <input type="text" name="judul_laporan" value="{{ old('judul_laporan') }}"
                        placeholder="Contoh: Anak mengalami kesulitan belajar"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('judul_laporan') border-red-300 @enderror">

                    @error('judul_laporan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Laporan</label>

                    <textarea name="deskripsi" rows="5" placeholder="Tuliskan permasalahan anak secara jelas..."
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none @error('deskripsi') border-red-300 @enderror">{{ old('deskripsi') }}</textarea>

                    @error('deskripsi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bukti --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bukti / Lampiran</label>

                    <label
                        class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 cursor-pointer bg-slate-50 hover:bg-blue-50 hover:border-blue-200 transition-colors">
                        <i data-feather="paperclip" class="w-4 h-4 text-slate-400"></i>

                        <span id="fileName" class="text-sm text-slate-400 truncate">
                            Foto, PDF, atau rekaman audio/video
                        </span>

                        <input type="file" name="bukti" id="buktiInput" class="hidden"
                            accept="image/*,.pdf,audio/*,video/*,.mp3,.mp4,.mov,.wav,.m4a,.ogg"
                            onchange="previewBukti(this)">
                    </label>

                    <p class="text-xs text-slate-400 mt-1">
                        Format: JPG, PNG, PDF, MP3, MP4, MOV, WAV · Maks 50MB
                    </p>

                    <div id="previewWrapper" class="hidden mt-3 rounded-xl overflow-hidden border border-slate-200">
                        <img id="previewImg" src="" alt="Preview" class="w-full object-cover max-h-56">
                    </div>

                    @error('bukti')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('orang_tua.laporan.index') }}"
                    class="px-5 py-2.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>

                <button type="submit"
                    class="px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>

    <script>
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
    </script>

@endsection