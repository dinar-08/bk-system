@extends('layouts.admin')

@section('title', 'Tambah Siswa')
@section('page-title', 'Tambah Siswa')
@section('page-subtitle', 'Tambahkan data siswa dan akun orang tua')

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.siswa.index') }}"
            class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-blue-700 transition-colors font-medium">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-sm text-slate-800 font-semibold">Tambah Siswa</span>
    </div>

    @if(session('password_baru'))
        <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-green-700 mb-1">Akun berhasil dibuat!</p>
            <p class="text-sm text-green-600">
                Nama:
                <strong>{{ session('nama_siswa_baru') }}</strong>
            </p>
            <p class="text-sm text-green-600">
                Password awal:
                <strong class="font-mono bg-green-100 px-2 py-0.5 rounded">
                    {{ session('password_baru') }}
                </strong>
            </p>
            <p class="text-xs text-green-500 mt-1">
                Simpan password ini dan berikan ke orang tua/wali. Akun wajib ganti password saat login pertama.
            </p>
        </div>
    @endif

    @if(session('import_selesai'))
        @php $hasil = session('import_hasil', ['berhasil' => [], 'gagal' => []]); @endphp

        <div class="mb-5 bg-white rounded-2xl border border-slate-200 p-6">
            <div class="mb-5 pb-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="font-bold text-slate-800">Hasil Import</h2>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Ringkasan data siswa yang berhasil dan gagal diimport.
                    </p>
                </div>

                @if(count($hasil['berhasil']) > 0)
                    <a href="{{ route('admin.import-siswa.download-hasil') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 transition-colors">
                        <i data-feather="download" class="w-4 h-4"></i>
                        Download Password
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Berhasil Diimport</p>
                    <h3 class="text-3xl font-bold text-green-700 mt-2">{{ count($hasil['berhasil']) }}</h3>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
                    <p class="text-xs font-semibold text-red-600 uppercase tracking-wider">Gagal Diimport</p>
                    <h3 class="text-3xl font-bold text-red-700 mt-2">{{ count($hasil['gagal']) }}</h3>
                </div>
            </div>

            @if(count($hasil['gagal']) > 0)
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Baris</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">NIS</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Alasan</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach($hasil['gagal'] as $g)
                                <tr>
                                    <td class="px-4 py-3 text-slate-500">{{ $g['baris'] }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $g['nis'] }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $g['nama'] }}</td>
                                    <td class="px-4 py-3 text-red-500">{{ $g['alasan'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-5">
                <div class="mb-5 pb-5 border-b border-slate-100 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <h2 class="font-bold text-slate-800 text-lg">Data Siswa</h2>
                        <p class="text-sm text-slate-500 mt-0.5">
                            NIS, nama siswa, dan kelas wajib diisi.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2">
                        <button type="button" onclick="openImportModal()"
                            class="inline-flex items-center gap-3 px-4 py-2.5 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl text-sm font-semibold hover:bg-blue-100 transition whitespace-nowrap">
                            <span class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center">
                                <i data-feather="upload-cloud" class="w-4 h-4 text-blue-700"></i>
                            </span>
                            <span class="text-left leading-tight">
                                <span class="block">Import Excel</span>
                            </span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Foto Siswa</label>
                        <input type="file" name="foto" accept="image/*"
                            class="w-full rounded-xl border border-slate-300 p-3 text-sm text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-slate-400 mt-1">Opsional. Boleh dikosongkan jika belum ada foto.</p>
                        @error('foto')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            NIS <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="nis" value="{{ old('nis') }}" required placeholder="Nomor Induk Siswa"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 @error('nis') border-red-300 @enderror">
                        @error('nis')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Nama Siswa <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="nama_siswa" value="{{ old('nama_siswa') }}" required
                            placeholder="Nama lengkap siswa"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 @error('nama_siswa') border-red-300 @enderror">
                        @error('nama_siswa')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Kelas <span class="text-red-400">*</span>
                        </label>
                        <select name="kelas" required
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 @error('kelas') border-red-300 @enderror">
                            <option value=""> Pilih Kelas </option>
                            @foreach(['1', '2', '3', '4', '5', '6'] as $t)
                                @foreach(['A', 'B', 'C', 'D'] as $h)
                                    <option value="{{ $t . $h }}" {{ old('kelas') === $t . $h ? 'selected' : '' }}>
                                        {{ $t . $h }}
                                    </option>
                                @endforeach
                            @endforeach
                            <option value="Internasional" {{ old('kelas') === 'Internasional' ? 'selected' : '' }}>
                                Internasional
                            </option>
                            <option value="Pindahan" {{ old('kelas') === 'Pindahan' ? 'selected' : '' }}>
                                Pindahan
                            </option>
                        </select>
                        @error('kelas')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Kelamin</label>
                        <select name="jenis_kelamin"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value=""> Belum diisi </option>
                            <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('tanggal_lahir')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor WhatsApp</label>
                        <input type="text" name="no_whatsapp" value="{{ old('no_whatsapp') }}" placeholder="08xxxxxxxxxx"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('no_whatsapp')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Orang Tua / Wali</label>
                        <input type="text" name="nama_ortu" value="{{ old('nama_ortu') }}" placeholder="Nama orang tua/wali"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('nama_ortu')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat</label>
                        <textarea name="alamat" rows="3" placeholder="Alamat siswa"
                            class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('alamat') }}</textarea>
                        @error('alamat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.siswa.index') }}"
                    class="px-5 py-2.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>

                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors">
                    <i data-feather="save" class="w-4 h-4"></i>
                    Simpan & Buat Akun
                </button>
            </div>
        </form>

        {{-- Modal Import Siswa --}}
        <div id="importModal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-900/40" onclick="closeImportModal()"></div>

            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-2xl p-6">
                    <div class="mb-5 pb-5 border-b border-slate-100 flex items-start justify-between gap-4">
                        <div>
                            <h2 class="font-bold text-slate-800">Import Data Siswa</h2>
                        </div>

                        <button type="button" onclick="closeImportModal()" class="text-slate-400 hover:text-slate-700">
                            <i data-feather="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.import-siswa.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="border-2 border-dashed border-slate-300 rounded-2xl p-10 text-center bg-slate-50 hover:border-blue-400 hover:bg-blue-50 transition-colors">
                            <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center mx-auto mb-4">
                                <i data-feather="file-plus" class="w-7 h-7 text-blue-700"></i>
                            </div>

                            <h3 class="text-sm font-bold text-slate-800">Pilih file Excel</h3>
                            <p class="text-sm text-slate-500 mt-1">
                                Format .xlsx atau .xls, maksimal 5MB.
                            </p>

                            <input type="file" name="file" id="file-input" accept=".xlsx,.xls" class="hidden"
                                onchange="updateFileName(this)">

                            <button type="button" onclick="document.getElementById('file-input').click()"
                                class="mt-5 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors">
                                <i data-feather="upload" class="w-4 h-4"></i>
                                Pilih File
                            </button>

                            <p id="file-name" class="text-sm font-semibold text-blue-700 mt-3 hidden"></p>
                        </div>


                        <div class="mt-5 bg-blue-50 border border-blue-100 rounded-2xl px-4 py-3 text-sm text-blue-700">
                            <div class="flex gap-2">
                                <i data-feather="info" class="w-4 h-4 mt-0.5 shrink-0"></i>
                                <p>
                                    Urutan kolom boleh acak, asalkan baris pertama memiliki header
                                    <strong>NIS</strong>, <strong>Nama Siswa</strong> atau <strong>Nama</strong>, dan
                                    <strong>Kelas</strong>.
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-5">
                            <button type="button" onclick="closeImportModal()"
                                class="px-5 py-2.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50">
                                Batal
                            </button>

                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors">
                                <i data-feather="check-circle" class="w-4 h-4"></i>
                                Upload & Proses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://unpkg.com/feather-icons"></script>
        <script>
            function openImportModal() {
                document.getElementById('importModal').classList.remove('hidden');
            }

            function closeImportModal() {
                document.getElementById('importModal').classList.add('hidden');
            }

            function updateFileName(input) {
                const nameEl = document.getElementById('file-name');

                if (input.files && input.files[0]) {
                    nameEl.textContent = input.files[0].name;
                    nameEl.classList.remove('hidden');
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }

                @if($errors->has('file'))
                    openImportModal();
                @endif
            });
        </script>

@endsection