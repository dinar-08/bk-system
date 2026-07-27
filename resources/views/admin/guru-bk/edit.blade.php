@extends('layouts.admin')
@section('title', 'Edit Guru BK')
@section('page-title', 'Edit Guru BK')
@section('page-subtitle', 'Perbarui data Guru BK')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.guru-bk.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">Edit Guru BK</span>
    </div>

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-6">

        <div class="mb-5 pb-5 border-b border-slate-100">
            <h2 class="font-bold text-slate-800">Informasi Guru BK</h2>
            <p class="text-sm text-slate-500 mt-0.5">Kosongkan password jika tidak ingin mengubahnya.</p>
        </div>

        <form action="{{ route('admin.guru-bk.update', $guruBk->nip) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Guru BK</label>
                    <input type="text" name="nama" value="{{ old('nama', $guruBk->nama) }}"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip', $guruBk->nip) }}"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $guruBk->no_hp) }}"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password Baru</label>
                    <input type="password" name="password"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="text-xs text-slate-400 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Foto</label>
                    <div class="flex items-center gap-4">
                        <div
                            class="w-16 h-16 rounded-full overflow-hidden bg-blue-100 flex items-center justify-center flex-shrink-0">
                            @if($guruBk->foto)
                                <img src="{{ route('foto.guru-bk', $guruBk->nip) }}" class="w-full h-full object-cover"
                                    alt="Foto {{ $guruBk->nama }}">
                            @else
                                <span
                                    class="text-blue-700 font-bold text-lg">{{ strtoupper(substr($guruBk->nama, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="foto" accept="image/*"
                                class="w-full text-sm border border-slate-300 rounded-xl px-3 py-2 focus:border-blue-500 focus:ring-blue-500">
                            <p class="text-xs text-slate-400 mt-1">Kosongkan jika tidak ingin mengubah foto.</p>
                        </div>
                    </div>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat</label>
                    <textarea name="alamat" rows="3"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('alamat', $guruBk->alamat) }}</textarea>
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('admin.guru-bk.index') }}"
                    class="px-5 py-2.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

@endsection