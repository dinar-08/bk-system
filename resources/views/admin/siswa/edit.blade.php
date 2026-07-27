@extends('layouts.admin')
@section('title', 'Edit Siswa')
@section('page-title', 'Edit Siswa')
@section('page-subtitle', 'Perbarui data siswa')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.siswa.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">Edit Siswa</span>
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

    <form action="{{ route('admin.siswa.update', $siswa->nis) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-5">
            <div class="mb-5 pb-5 border-b border-slate-100">
                <h2 class="font-bold text-slate-800">Data Siswa</h2>
                <p class="text-sm text-slate-500 mt-0.5">Perbarui informasi pribadi siswa.</p>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Foto Siswa</label>

                    <div
                        class="w-24 h-24 rounded-2xl overflow-hidden bg-blue-50 border border-slate-200 flex items-center justify-center mb-3 shadow-sm">
                        @if($siswa->foto)
                            <img src="{{ route('foto.siswa', $siswa->nis) }}" class="w-full h-full object-cover"
                                alt="Foto Siswa">
                        @else
                            <i data-feather="user" class="w-8 h-8 text-blue-200"></i>
                        @endif
                    </div>

                    <input type="file" name="foto" accept="image/*"
                        class="w-full rounded-xl border border-slate-300 p-3 text-sm text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-slate-400 mt-1">Kosongkan jika tidak ingin mengubah foto.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">NIS</label>
                    <input type="text" name="nis" value="{{ old('nis', $siswa->nis) }}"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Siswa</label>
                    <input type="text" name="nama_siswa" value="{{ old('nama_siswa', $siswa->nama_siswa) }}"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kelas</label>
                    <input type="text" name="kelas" value="{{ old('kelas', $siswa->kelas) }}"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Kelamin</label>
                    <select name="jenis_kelamin"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-Laki
                        </option>
                        <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor WhatsApp</label>
                    <input type="text" name="no_whatsapp" value="{{ old('no_whatsapp', $siswa->no_whatsapp) }}"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Orang Tua / Wali</label>
                    <input type="text" name="nama_ortu" value="{{ old('nama_ortu', $siswa->nama_ortu) }}"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat</label>
                    <textarea name="alamat" rows="3"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('alamat', $siswa->alamat) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-5">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password Baru</label>
                <input type="password" name="password"
                    class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="text-xs text-slate-400 mt-1">Minimal 8 karakter jika diisi.</p>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.siswa.index') }}"
                class="px-5 py-2.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                class="px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors">
                Simpan Perubahan
            </button>
        </div>

    </form>

@endsection