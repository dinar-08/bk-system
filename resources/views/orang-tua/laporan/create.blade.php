@extends('layouts.orang-tua')
@section('title', 'Buat Laporan')
@section('page-title', 'Buat Laporan')
@section('page-subtitle', 'Sampaikan permasalahan anak Anda kepada Guru BK')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('orang_tua.laporan.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">Buat Laporan</span>
    </div>

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

        <div class="mb-5 pb-5 border-b border-slate-100">
            <h2 class="font-bold text-slate-800">Formulir Laporan</h2>
            <p class="text-sm text-slate-500 mt-0.5">Semua informasi bersifat rahasia dan hanya dilihat oleh Guru BK.</p>
        </div>

        <form action="{{ route('orang_tua.laporan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Judul --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Judul Laporan</label>
                <input type="text" name="judul_laporan" value="{{ old('judul_laporan') }}"
                    placeholder="Contoh: Anak sering terlihat murung di rumah"
                    class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 @error('judul_laporan') border-red-300 @enderror">
                @error('judul_laporan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Bukti --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Bukti / Lampiran
                    <span class="text-slate-400 font-normal text-xs ml-1">(opsional)</span>
                </label>
                <label
                    class="flex items-center gap-3 px-4 py-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-colors">
                    <i data-feather="paperclip" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                    <span id="fileName" class="text-sm text-slate-400 truncate">Pilih file JPG, PNG, atau PDF — maks. 2
                        MB</span>
                    <input type="file" name="bukti" id="buktiInput" accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                </label>
                <div id="previewWrapper" class="hidden mt-3 rounded-xl overflow-hidden border border-slate-200">
                    <img id="previewImg" src="" alt="Preview" class="w-full object-cover max-h-48">
                </div>
                @error('bukti')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Laporan</label>
                <textarea name="deskripsi" rows="6"
                    placeholder="Tuliskan permasalahan atau kondisi anak yang ingin disampaikan kepada Guru BK..."
                    class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 resize-none @error('deskripsi') border-red-300 @enderror">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
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
        document.getElementById('buktiInput').addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                document.getElementById('fileName').textContent = file.name;
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
        });
    </script>

@endsection