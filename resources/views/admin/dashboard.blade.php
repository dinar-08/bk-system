@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5">

        {{-- Jumlah Siswa --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="h-1.5 bg-blue-500"></div>

            <div class="p-4 sm:p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Jumlah Siswa
                    </p>

                    <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900">
                        {{ $jumlahSiswa ?? 0 }}
                    </h2>
                </div>

                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <i data-feather="users" class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        {{-- Guru BK --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="h-1.5 bg-emerald-500"></div>

            <div class="p-4 sm:p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Guru BK
                    </p>

                    <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900">
                        {{ $totalGuruBK ?? 0 }}
                    </h2>
                </div>

                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                    <i data-feather="user-check" class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600"></i>
                </div>
            </div>
        </div>

        {{-- Siswa Tidak Aktif --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="h-1.5 bg-orange-500"></div>

            <div class="p-4 sm:p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Siswa Tidak Aktif
                    </p>

                    <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900">
                        {{ $siswaTidakAktif ?? 0 }}
                    </h2>
                </div>

                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                    <i data-feather="archive" class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600"></i>
                </div>
            </div>
        </div>

        {{-- Permasalahan Siswa --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="h-1.5 bg-red-500"></div>

            <div class="p-4 sm:p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Permasalahan Siswa
                    </p>

                    <h2 class="mt-2 text-2xl sm:text-3xl font-bold text-slate-900">
                        {{ $totalPermasalahan ?? 0 }}
                    </h2>
                </div>

                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                    <i data-feather="alert-circle" class="w-5 h-5 sm:w-6 sm:h-6 text-red-600"></i>
                </div>
            </div>
        </div>

    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>

@endsection