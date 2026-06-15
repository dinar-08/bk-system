
@extends('layouts.admin')
@section('title', 'Periode Update Data')
@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Periode Wajib Update Data Siswa</h2>
            <p class="text-sm text-gray-500 mt-0.5">Set periode di mana siswa wajib memperbarui data diri</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form buat periode baru --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Buat Periode Baru</h3>
                <form method="POST" action="{{ route('admin.periode-update.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" placeholder="2026/2027" required
                            class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('tahun_ajaran')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required
                            class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required
                            class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl text-sm transition">
                        Simpan Periode
                    </button>
                </form>
            </div>

            @if($aktifSekarang)
            <div class="mt-4 bg-amber-50 border border-amber-200 rounded-2xl p-4">
                <p class="text-xs font-semibold text-amber-700 mb-1">⚡ Periode Aktif Sekarang</p>
                <p class="text-sm font-bold text-amber-800">{{ $aktifSekarang->tahun_ajaran }}</p>
                <p class="text-xs text-amber-600 mt-1">
                    {{ \Carbon\Carbon::parse($aktifSekarang->tanggal_mulai)->format('d M Y') }} –
                    {{ \Carbon\Carbon::parse($aktifSekarang->tanggal_selesai)->format('d M Y') }}
                </p>
            </div>
            @endif
        </div>

        {{-- Rekap siswa + riwayat periode --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Rekap siswa sudah/belum update --}}
            @if($aktifSekarang && count($rekapSiswa) > 0)
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-800">Status Update Siswa</h3>
                    <div class="flex gap-3 text-xs">
                        <span class="text-green-600 font-medium">✓ {{ collect($rekapSiswa)->where('sudah_update', true)->count() }} sudah</span>
                        <span class="text-red-500 font-medium">✗ {{ collect($rekapSiswa)->where('sudah_update', false)->count() }} belum</span>
                    </div>
                </div>
                <div class="overflow-x-auto max-h-64 overflow-y-auto rounded-xl border border-gray-100">
                    <table class="w-full text-xs">
                        <thead class="sticky top-0 bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Nama</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Kelas</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Status</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Update Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekapSiswa as $s)
                            <tr class="border-t border-gray-100">
                                <td class="px-3 py-2 text-gray-700">{{ $s['nama'] }}</td>
                                <td class="px-3 py-2 text-gray-500">{{ $s['kelas'] ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    @if($s['sudah_update'])
                                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Sudah</span>
                                    @else
                                        <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-medium">Belum</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-gray-400">{{ $s['update_at'] ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Riwayat periode --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Riwayat Periode</h3>
                @forelse($periode as $p)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $p->tahun_ajaran }}</p>
                        <p class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }} –
                            {{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($p->aktif && $p->tanggal_mulai <= now()->toDateString() && $p->tanggal_selesai >= now()->toDateString())
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-medium">Aktif</span>
                        @elseif($p->tanggal_selesai < now()->toDateString())
                            <span class="bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full">Selesai</span>
                        @else
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">Mendatang</span>
                        @endif
                        {{-- Tombol perpanjang --}}
                        <form method="POST" action="{{ route('admin.periode-update.update', $p->id) }}" class="flex gap-1">
                            @csrf @method('PATCH')
                            <input type="date" name="tanggal_selesai" value="{{ $p->tanggal_selesai }}"
                                class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:ring-1 focus:ring-blue-400 focus:outline-none">
                            <button type="submit" class="bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs px-2 py-1 rounded-lg transition">Perpanjang</button>
                        </form>
                        <form method="POST" action="{{ route('admin.periode-update.destroy', $p->id) }}">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Hapus periode ini?')"
                                class="text-red-400 hover:text-red-600 text-xs px-2 py-1 rounded-lg transition">Hapus</button>
                        </form>
                    </div>
                </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada periode yang dibuat.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection



