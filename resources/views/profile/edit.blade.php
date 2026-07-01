@extends(
    auth()->user()->role === 'admin' ? 'layouts.admin' :
    (auth()->user()->role === 'bk' ? 'layouts.bk' : 'layouts.orang-tua')
)

@section('title', 'Profil')

@section('content')
@php
    $role = auth()->user()->role;

    $displayName = $role === 'orang_tua'
        ? ($dataProfil->nama_siswa ?? $user->name)
        : $user->name;

    $roleLabel = match ($role) {
        'admin' => 'Administrator',
        'bk'    => 'Guru BK',
        default => 'Siswa',
    };

    // Foto: orang_tua ambil dari siswa, lainnya dari users
    $photo = $role === 'orang_tua'
        ? ($dataProfil->foto ?? null)
        : ($role === 'admin' ? ($user->foto ?? null) : ($dataProfil->foto ?? null));

    $mustChangePassword = auth()->user()->must_change_password;

    $wajibIsiLengkap = $role === 'orang_tua' && ($mustChangePassword || (!empty($wajibUpdate) && $wajibUpdate));

    // Foto wajib hanya jika belum ada foto sama sekali
    $fotoWajib = $wajibIsiLengkap && !$photo;
@endphp

<div class="space-y-6">

    {{-- ✅ Profil berhasil disimpan --}}
    @if(session('status') === 'profile-updated')
        <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 flex gap-3">
            <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-green-700">Profil berhasil diperbarui.</p>
                <p class="text-sm text-green-600 mt-1">Data profil telah berhasil disimpan.</p>
            </div>
        </div>
    @endif

    {{-- 🔴 Wajib ganti password (password dari sistem) --}}
    @if($mustChangePassword)
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5 flex gap-3">
            <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v2m0 4h.01M10.29 3.86l-7.5 13A2 2 0 004.5 20h15a2 2 0 001.71-3.14l-7.5-13a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <h4 class="font-semibold text-red-800">Wajib Ganti Password & Lengkapi Data</h4>
                <p class="text-sm text-red-700 mt-1">
                    Anda masih menggunakan password dari sistem. Silakan ganti password dan lengkapi
                    semua data profil sebelum dapat menggunakan fitur lainnya.
                </p>
            </div>
        </div>
    @endif

    {{-- 🟡 Wajib update data (periode update aktif) --}}
    @if(!$mustChangePassword && !empty($wajibUpdate) && $wajibUpdate)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 flex gap-3">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v2m0 4h.01M10.29 3.86l-7.5 13A2 2 0 004.5 20h15a2 2 0 001.71-3.14l-7.5-13a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <h4 class="font-semibold text-amber-800">Periode Update Aktif</h4>
                <p class="text-sm text-amber-700 mt-1">
                    Sedang berlangsung periode pembaruan data
                    <strong>{{ $periode->nama ?? '' }}</strong>.
                    Semua data wajib diperbarui dan disimpan sebelum menggunakan fitur lainnya.
                </p>
            </div>
        </div>
    @endif

    {{-- Session warning dari middleware --}}
    @if(session('warning'))
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 flex gap-3">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v2m0 4h.01M10.29 3.86l-7.5 13A2 2 0 004.5 20h15a2 2 0 001.71-3.14l-7.5-13a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <h4 class="font-semibold text-amber-800">Perbarui Data Profil</h4>
                <p class="text-sm text-amber-700 mt-1">{{ session('warning') }}</p>
            </div>
        </div>
    @endif

    {{-- Validasi error --}}
    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-center gap-3 text-sm">
        @if(!$mustChangePassword && !($wajibUpdate ?? false))
            <a href="javascript:history.back()" class="text-slate-500 hover:text-blue-700">Kembali</a>
            <span class="text-slate-300">/</span>
        @endif
        <span class="font-bold text-slate-900">Profil Pengguna</span>
    </div>

    <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-sm">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf
            @method('PATCH')

            {{-- FOTO PROFIL --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-5 mb-8 pb-8 border-b border-slate-200">
                <div class="shrink-0">
                    <label for="foto-input" class="cursor-pointer block">
                        @if($photo)
                            <img id="avatar-preview"
                                src="{{ asset('storage/' . $photo) }}"
                                class="w-24 h-24 object-cover border-4 {{ $fotoWajib ? 'border-red-300' : 'border-blue-100' }}">
                        @else
                            <div id="avatar-initials"
                                class="w-24 h-24 flex items-center justify-center text-3xl font-bold text-white border-4
                                    {{ $fotoWajib ? 'bg-red-400 border-red-300' : 'bg-blue-700 border-blue-100' }}">
                                {{ strtoupper(substr($displayName, 0, 1)) }}
                            </div>
                            <img id="avatar-preview" src=""
                                class="hidden w-24 h-24 object-cover border-4 border-blue-100">
                        @endif
                    </label>

                    @if($fotoWajib)
                        <p class="text-xs text-red-500 mt-1 text-center">Wajib upload foto</p>
                    @endif
                </div>

                <div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $displayName }}</h2>
                    <p class="text-sm text-slate-500 mt-1">{{ $roleLabel }}</p>
                    <p class="text-xs text-slate-400 mt-2">
                        Klik foto untuk mengganti foto profil.
                        @if($fotoWajib)
                            <span class="text-red-500 font-semibold">Foto wajib diunggah.</span>
                        @endif
                    </p>
                </div>

                <input type="file" id="foto-input" name="foto"
                    accept="image/jpeg,image/png,image/gif"
                    {{ $fotoWajib ? 'required' : '' }}
                    class="hidden">
            </div>

            {{-- INFORMASI DASAR --}}
            <div class="mb-6">
                <h3 class="text-base font-bold text-slate-900">Informasi Dasar</h3>
                <p class="text-sm text-slate-500 mt-1">
                    Data akun utama pengguna.
                    @if($wajibIsiLengkap)
                        <span class="text-red-500 font-semibold">Semua field wajib diisi.</span>
                    @endif
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @if($role === 'orang_tua' && $dataProfil)
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Nama Siswa @if($wajibIsiLengkap) <span class="text-red-500">*</span> @endif
                        </label>
                        <input type="text" name="nama_siswa"
                            value="{{ old('nama_siswa', $dataProfil->nama_siswa) }}"
                            {{ $wajibIsiLengkap ? 'required' : '' }}
                            class="w-full rounded-2xl border px-4 py-3 text-sm focus:ring-4 focus:ring-blue-100
                                {{ $wajibIsiLengkap && !$dataProfil->nama_siswa ? 'border-red-300 bg-red-50 focus:border-red-500' : 'border-slate-300 bg-white focus:border-blue-600' }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">NIS</label>
                        <input type="text" value="{{ $dataProfil->nis }}" readonly
                            class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-400 cursor-not-allowed">
                        <p class="text-xs text-slate-400 mt-1">Hanya dapat diubah oleh admin.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Kelas @if($wajibIsiLengkap) <span class="text-red-500">*</span> @endif
                        </label>
                        <input type="text" name="kelas"
                            value="{{ old('kelas', $dataProfil->kelas) }}"
                            {{ $wajibIsiLengkap ? 'required' : '' }}
                            class="w-full rounded-2xl border px-4 py-3 text-sm focus:ring-4 focus:ring-blue-100
                                {{ $wajibIsiLengkap && !$dataProfil->kelas ? 'border-red-300 bg-red-50 focus:border-red-500' : 'border-slate-300 bg-white focus:border-blue-600' }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Jenis Kelamin @if($wajibIsiLengkap) <span class="text-red-500">*</span> @endif
                        </label>
                        <select name="jenis_kelamin"
                            {{ $wajibIsiLengkap ? 'required' : '' }}
                            class="w-full rounded-2xl border px-4 py-3 text-sm focus:ring-4 focus:ring-blue-100
                                {{ $wajibIsiLengkap && !$dataProfil->jenis_kelamin ? 'border-red-300 bg-red-50 focus:border-red-500' : 'border-slate-300 bg-white focus:border-blue-600' }}">
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin', $dataProfil->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $dataProfil->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Tanggal Lahir @if($wajibIsiLengkap) <span class="text-red-500">*</span> @endif
                        </label>
                        <input type="date" name="tanggal_lahir"
                            value="{{ old('tanggal_lahir', optional($dataProfil->tanggal_lahir)->format('Y-m-d')) }}"
                            {{ $wajibIsiLengkap ? 'required' : '' }}
                            class="w-full rounded-2xl border px-4 py-3 text-sm focus:ring-4 focus:ring-blue-100
                                {{ $wajibIsiLengkap && !$dataProfil->tanggal_lahir ? 'border-red-300 bg-red-50 focus:border-red-500' : 'border-slate-300 bg-white focus:border-blue-600' }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Nama Orang Tua @if($wajibIsiLengkap) <span class="text-red-500">*</span> @endif
                        </label>
                        <input type="text" name="nama_ortu"
                            value="{{ old('nama_ortu', $dataProfil->nama_ortu) }}"
                            {{ $wajibIsiLengkap ? 'required' : '' }}
                            class="w-full rounded-2xl border px-4 py-3 text-sm focus:ring-4 focus:ring-blue-100
                                {{ $wajibIsiLengkap && !$dataProfil->nama_ortu ? 'border-red-300 bg-red-50 focus:border-red-500' : 'border-slate-300 bg-white focus:border-blue-600' }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Nomor WhatsApp @if($wajibIsiLengkap) <span class="text-red-500">*</span> @endif
                        </label>
                        <input type="text" name="no_whatsapp"
                            value="{{ old('no_whatsapp', $dataProfil->no_whatsapp) }}"
                            {{ $wajibIsiLengkap ? 'required' : '' }}
                            class="w-full rounded-2xl border px-4 py-3 text-sm focus:ring-4 focus:ring-blue-100
                                {{ $wajibIsiLengkap && !$dataProfil->no_whatsapp ? 'border-red-300 bg-red-50 focus:border-red-500' : 'border-slate-300 bg-white focus:border-blue-600' }}">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Alamat Siswa @if($wajibIsiLengkap) <span class="text-red-500">*</span> @endif
                        </label>
                        <textarea name="alamat" rows="3"
                            {{ $wajibIsiLengkap ? 'required' : '' }}
                            class="w-full rounded-2xl border px-4 py-3 text-sm focus:ring-4 focus:ring-blue-100 resize-none
                                {{ $wajibIsiLengkap && !$dataProfil->alamat ? 'border-red-300 bg-red-50 focus:border-red-500' : 'border-slate-300 bg-white focus:border-blue-600' }}">{{ old('alamat', $dataProfil->alamat) }}</textarea>
                    </div>

                @else
                    {{-- BAGIAN ADMIN DAN BK --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Akun</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            {{ $role === 'bk' ? 'NIP' : 'Username' }}
                        </label>
                        <input type="text" value="{{ $user->username }}" readonly
                            class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-400 cursor-not-allowed">
                        <p class="text-xs text-slate-400 mt-1">Hanya dapat diubah oleh admin.</p>
                    </div>

                    @if($role === 'bk' && $dataProfil)
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor HP</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $dataProfil->no_hp) }}"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat</label>
                            <input type="text" name="alamat" value="{{ old('alamat', $dataProfil->alamat) }}"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                        </div>
                    @endif
                @endif
            </div>

            <div class="my-8 border-t border-slate-200"></div>

            {{-- BAGIAN GANTI PASSWORD --}}
            <div class="mb-6">
                <h3 class="text-base font-bold text-slate-900">
                    Ganti Password
                    @if($mustChangePassword)
                        <span class="ml-2 text-xs font-semibold text-white bg-red-500 px-2 py-0.5 rounded-full">Wajib</span>
                    @endif
                </h3>
                <p class="text-sm mt-1 {{ $mustChangePassword ? 'text-red-600 font-medium' : 'text-slate-500' }}">
                    @if($mustChangePassword)
                        Anda wajib mengganti password sebelum dapat menggunakan fitur lainnya.
                        Password harus minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka.
                    @else
                        Kosongkan kedua kolom jika tidak ingin mengganti password.
                    @endif
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Password Baru
                        @if($mustChangePassword) <span class="text-red-500">*</span> @endif
                    </label>
                    <input type="password"
                        name="password"
                        autocomplete="new-password"
                        readonly
                        onfocus="this.removeAttribute('readonly')"
                        placeholder="{{ $mustChangePassword ? 'Wajib diisi' : 'Masukkan Password Baru' }}"
                        {{ $mustChangePassword ? 'required' : '' }}
                        class="w-full rounded-2xl border px-4 py-3 text-sm focus:ring-4 focus:ring-blue-100
                            {{ $mustChangePassword ? 'border-red-300 bg-red-50 focus:border-red-500' : 'border-slate-300 bg-white focus:border-blue-600' }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Konfirmasi Password
                        @if($mustChangePassword) <span class="text-red-500">*</span> @endif
                    </label>
                    <input type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        readonly
                        onfocus="this.removeAttribute('readonly')"
                        placeholder="{{ $mustChangePassword ? 'Wajib diisi' : 'Ulangi Password Baru' }}"
                        {{ $mustChangePassword ? 'required' : '' }}
                        class="w-full rounded-2xl border px-4 py-3 text-sm focus:ring-4 focus:ring-blue-100
                            {{ $mustChangePassword ? 'border-red-300 bg-red-50 focus:border-red-500' : 'border-slate-300 bg-white focus:border-blue-600' }}">
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-200 flex justify-end gap-3">
                @if(!$mustChangePassword && !($wajibUpdate ?? false))
                    <a href="javascript:history.back()"
                        class="px-6 py-3 rounded-2xl border border-slate-300 text-slate-600 text-sm font-bold hover:bg-slate-50">
                        Batal
                    </a>
                @endif

                <button type="submit"
                    class="px-6 py-3 rounded-2xl text-white text-sm font-bold
                        {{ $mustChangePassword || ($wajibUpdate ?? false)
                            ? 'bg-red-600 hover:bg-red-700'
                            : 'bg-blue-700 hover:bg-blue-800' }}">
                    {{ $mustChangePassword || ($wajibUpdate ?? false) ? 'Simpan & Lanjutkan' : 'Simpan Perubahan' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const input    = document.getElementById('foto-input');
    const preview  = document.getElementById('avatar-preview');
    const initials = document.getElementById('avatar-initials');

    if (input) {
        input.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                if (initials) {
                    initials.classList.add('hidden');
                }
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endsection