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
        'bk' => 'Guru BK',
        default => 'Orang Tua / Wali',
    };

    $photo = $role === 'admin'
        ? ($user->foto ?? null)
        : ($dataProfil->foto ?? null);
@endphp

<div class="space-y-6">

    @if(session('status'))
        <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

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
        <a href="javascript:history.back()" class="text-slate-500 hover:text-blue-700">Kembali</a>
        <span class="text-slate-300">/</span>
        <span class="font-bold text-slate-900">Profil Pengguna</span>
    </div>

    <div class="rounded-3xl bg-white border border-slate-200 p-8 shadow-sm">

        <div class="mb-7">
            <h2 class="text-xl font-bold text-slate-900">Profil</h2>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="flex items-center gap-5 mb-8 pb-8 border-b border-slate-200">
                <label for="foto-input" class="cursor-pointer shrink-0">
                    @if($photo)
                        <img id="avatar-preview" src="{{ asset('storage/' . $photo) }}"
                            class="w-24 h-24 rounded-full object-cover border-4 border-blue-100">
                    @else
                        <div id="avatar-initials"
                            class="w-24 h-24 rounded-full bg-blue-700 flex items-center justify-center text-3xl font-bold text-white border-4 border-blue-100">
                            {{ strtoupper(substr($displayName, 0, 1)) }}
                        </div>

                        <img id="avatar-preview" src=""
                            class="hidden w-24 h-24 rounded-full object-cover border-4 border-blue-100">
                    @endif
                </label>

                <div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $displayName }}</h3>
                    <p class="text-sm text-slate-500 mt-1">{{ $roleLabel }}</p>
                    <p class="text-xs text-slate-400 mt-2">Klik foto untuk mengganti foto profil.</p>
                </div>

                <input type="file" id="foto-input" name="foto" accept="image/jpeg,image/png,image/gif" class="hidden">
            </div>

            <div class="mb-8">
                <h3 class="text-base font-bold text-slate-900">Informasi Dasar</h3>
                <p class="text-sm text-slate-500 mt-1">Data akun utama pengguna.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                @if($role === 'orang_tua' && $dataProfil)

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Siswa</label>
                        <input type="text" name="nama_siswa"
                            value="{{ old('nama_siswa', $dataProfil->nama_siswa) }}"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">NIS</label>
                        <input type="text" value="{{ $dataProfil->nis }}" readonly
                            class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-400 cursor-not-allowed">
                        <p class="text-xs text-slate-400 mt-1">Hanya dapat diubah oleh admin.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Kelas</label>
                        <input type="text" name="kelas"
                            value="{{ old('kelas', $dataProfil->kelas) }}"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Kelamin</label>
                        <select name="jenis_kelamin"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                            <option value="L" {{ old('jenis_kelamin', $dataProfil->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $dataProfil->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir"
                            value="{{ old('tanggal_lahir', $dataProfil->tanggal_lahir) }}"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Orang Tua</label>
                        <input type="text" name="nama_ortu"
                            value="{{ old('nama_ortu', $dataProfil->nama_ortu) }}"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor WhatsApp</label>
                        <input type="text" name="no_whatsapp"
                            value="{{ old('no_whatsapp', $dataProfil->no_whatsapp) }}"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Siswa</label>
                        <textarea name="alamat" rows="3"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100 resize-none">{{ old('alamat', $dataProfil->alamat) }}</textarea>
                    </div>

                @else

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

            <div class="mb-6">
                <h3 class="text-base font-bold text-slate-900">Ganti Password</h3>
                <p class="text-sm text-slate-500 mt-1">Kosongkan jika tidak ingin mengganti password.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password Baru</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diganti"
                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-200 flex justify-end gap-3">
                <a href="javascript:history.back()"
                    class="px-6 py-3 rounded-2xl border border-slate-300 text-slate-600 text-sm font-bold hover:bg-slate-50">
                    Batal
                </a>

                <button type="submit"
                    class="px-6 py-3 rounded-2xl bg-blue-700 text-white text-sm font-bold hover:bg-blue-800">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const input = document.getElementById('foto-input');
    const preview = document.getElementById('avatar-preview');
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