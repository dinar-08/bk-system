@extends('layouts.orang-tua')
@section('title', 'Perbarui Data Diri')
@section('content')
    <div class="max-w-2xl mx-auto">
        @if($periode)
            <div class="mb-5 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                <p class="text-sm font-semibold text-amber-700">⚡ Periode Wajib Update Data Aktif</p>
                <p class="text-xs text-amber-600 mt-1">
                    Tahun Ajaran {{ $periode->tahun_ajaran }} ·
                    Berlaku s/d {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d M Y') }}
                </p>
                <p class="text-xs text-amber-600 mt-0.5">Perbarui data diri Anda untuk bisa mengakses semua fitur.</p>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-800">Perbarui Data Siswa</h2>
                <p class="text-xs text-gray-400 mt-0.5">NIS tidak dapat diubah.</p>
            </div>

            <form method="POST" action="{{ route('orang_tua.update-data.save') }}" enctype="multipart/form-data"
                class="p-6 space-y-4">
                @csrf @method('PATCH')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">NIS <span class="text-gray-400">(tidak
                                bisa diubah)</span></label>
                        <input type="text" value="{{ $siswa->nis }}" disabled
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 text-gray-400">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Nama Lengkap <span
                                class="text-red-400">*</span></label>
                        <input type="text" name="nama_siswa" value="{{ old('nama_siswa', $siswa->nama_siswa) }}" required
                            class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none @error('nama_siswa') border-red-400 @enderror">
                        @error('nama_siswa')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Kelas <span
                                class="text-red-400">*</span></label>
                        <select name="kelas" required
                            class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            @php
                                $daftarKelas = [];
                                foreach (['1', '2', '3', '4', '5', '6'] as $tingkat) {
                                    foreach (['A', 'B', 'C', 'D'] as $huruf) {
                                        $daftarKelas[] = $tingkat . $huruf;
                                    }
                                }
                                $daftarKelas[] = 'Internasional';
                                $daftarKelas[] = 'Pindahan';
                            @endphp
                            @foreach($daftarKelas as $k)
                                <option value="{{ $k }}" {{ old('kelas', $siswa->kelas) === $k ? 'selected' : '' }}>{{ $k }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Jenis Kelamin <span
                                class="text-red-400">*</span></label>
                        <select name="jenis_kelamin" required
                            class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) === 'L' ? 'selected' : '' }}>
                                Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) === 'P' ? 'selected' : '' }}>
                                Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Nama Orang Tua <span
                                class="text-red-400">*</span></label>
                        <input type="text" name="nama_ortu" value="{{ old('nama_ortu', $siswa->nama_ortu) }}" required
                            class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">No. WhatsApp <span
                                class="text-red-400">*</span></label>
                        <input type="text" name="no_whatsapp" value="{{ old('no_whatsapp', $siswa->no_whatsapp) }}" required
                            placeholder="08xxxxxxxxxx"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1">Alamat</label>
                    <textarea name="alamat" rows="2"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('alamat', $siswa->alamat) }}</textarea>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1">Foto Siswa</label>
                    @if($siswa->foto)
                        <img src="{{ Storage::url($siswa->foto) }}"
                            class="w-16 h-16 rounded-full object-cover border border-gray-200 mb-2">
                    @endif
                    <input type="file" name="foto" accept="image/jpg,image/jpeg,image/png"
                        class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl text-sm transition">
                        Simpan & Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection