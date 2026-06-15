
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password | Lapor Bu!!</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800">Ganti Password</h2>
            <p class="text-sm text-gray-500 mt-1">Anda wajib mengganti password sebelum melanjutkan. Gunakan kombinasi
                huruf besar, huruf kecil, dan angka.</p>
        </div>

        @if(session('info'))
            <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl text-sm">
                {{ session('info') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.change.process') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror"
                    placeholder="Minimal 8 karakter, huruf + angka">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ulangi password baru">
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl text-sm transition">
                Simpan & Lanjutkan
            </button>
        </form>
    </div>
</body>

</html>
EOF

# View: Import siswa
mkdir -p /home/claude/al-kautsar-fix/resources/views/admin/import-siswa

cat > /home/claude/al-kautsar-fix/resources/views/admin/import-siswa/index.blade.php << 'BLADE'
    @extends('layouts.admin') @section('title', 'Import Siswa') @section('content') <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Import Siswa dari Excel</h2>
                <p class="text-sm text-gray-500 mt-0.5">Upload file Excel untuk menambah banyak siswa sekaligus</p>
            </div>
            <a href="{{ route('admin.import-siswa.template') }}"
                class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download Template
            </a>
        </div>

        {{-- Form upload --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
            <form method="POST" action="{{ route('admin.import-siswa.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center mb-4 hover:border-blue-400 transition"
                    id="dropzone">
                    <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-sm text-gray-500 mb-2">Pilih file Excel atau drag & drop di sini</p>
                    <p class="text-xs text-gray-400">.xlsx atau .xls • Maks 5MB</p>
                    <input type="file" name="file" id="file-input" accept=".xlsx,.xls" class="hidden"
                        onchange="updateFileName(this)">
                    <button type="button" onclick="document.getElementById('file-input').click()"
                        class="mt-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        Pilih File
                    </button>
                    <p id="file-name" class="text-sm font-medium text-blue-600 mt-2 hidden"></p>
                </div>
                @error('file')<p class="text-red-500 text-sm mb-3">{{ $message }}</p>@enderror

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700 mb-4">
                    <p class="font-semibold mb-1">Format kolom Excel:</p>
                    <p>Kolom A: NIS · Kolom B: Nama Siswa · Kolom C: Kelas</p>
                    <p class="mt-1">Baris 1 = header, data mulai dari baris 2. Password akan di-generate otomatis per siswa.
                    </p>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl text-sm transition">
                    Upload & Proses
                </button>
            </form>
        </div>

        {{-- Hasil import --}}
        @if(session('import_selesai'))
            @php $hasil = session('import_hasil', ['berhasil' => [], 'gagal' => []]); @endphp
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Hasil Import</h3>
                    @if(count($hasil['berhasil']) > 0)
                        <a href="{{ route('admin.import-siswa.download-hasil') }}"
                            class="flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Password Siswa
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-center">
                        <p class="text-2xl font-bold text-green-600">{{ count($hasil['berhasil']) }}</p>
                        <p class="text-xs text-green-600 mt-0.5">Berhasil diimport</p>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-center">
                        <p class="text-2xl font-bold text-red-500">{{ count($hasil['gagal']) }}</p>
                        <p class="text-xs text-red-500 mt-0.5">Gagal diimport</p>
                    </div>
                </div>

                @if(count($hasil['gagal']) > 0)
                    <p class="text-sm font-medium text-gray-700 mb-2">Baris yang gagal:</p>
                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Baris</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">NIS</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Nama</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hasil['gagal'] as $g)
                                    <tr class="border-t border-gray-100">
                                        <td class="px-3 py-2 text-gray-500">{{ $g['baris'] }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $g['nis'] }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $g['nama'] }}</td>
                                        <td class="px-3 py-2 text-red-500">{{ $g['alasan'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
        </div>

        <script>
            function updateFileName(input) {
                const nameEl = document.getElementById('file-name');
                if (input.files && input.files[0]) {
                    nameEl.textContent = '✓ ' + input.files[0].name;
                    nameEl.classList.remove('hidden');
                }
            }
        </script>
    @endsection