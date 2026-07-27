@extends('layouts.admin')
@section('title', 'Data Guru BK')
@section('page-title', 'Data Guru BK')
@section('page-subtitle', 'Kelola akun dan data Guru BK')

@section('content')

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Data Guru BK</h1>
            <p class="text-slate-500 mt-1 text-sm">Kelola akun dan data Guru BK.</p>
        </div>
        <a href="{{ route('admin.guru-bk.create') }}"
            class="flex items-center gap-2 px-4 py-2.5 bg-blue-700 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Guru BK
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-4 text-left font-semibold text-slate-600">Foto</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-600">Nama</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-600">NIP</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-600">Nomor WA</th>
                    <th class="px-5 py-4 text-left font-semibold text-slate-600">Alamat</th>
                    <th class="px-5 py-4 text-center font-semibold text-slate-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guruBk as $item)
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="w-14 h-14 rounded-full overflow-hidden bg-blue-100 flex items-center justify-center">
                                @if($item->foto)
                                    <img src="{{ route('foto.guru-bk', $item->nip) }}" class="w-full h-full object-cover"
                                        alt="Foto {{ $item->nama }}">
                                @else
                                    <span class="text-blue-700 font-bold text-sm">{{ strtoupper(substr($item->nama, 0, 1)) }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-medium text-slate-800">{{ $item->nama }}</span>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $item->nip }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $item->no_hp }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $item->alamat }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.guru-bk.edit', $item->nip) }}"
                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('admin.guru-bk.destroy', $item->nip) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Hapus data Guru BK ini?')"
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-lg text-xs font-semibold hover:bg-red-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-14 text-center">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="text-slate-400 text-sm">Belum ada data Guru BK.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection