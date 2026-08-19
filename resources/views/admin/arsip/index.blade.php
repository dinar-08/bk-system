@extends('layouts.admin')

@section('title', 'Arsip Siswa')
@section('page-title', 'Arsip Siswa')
@section('page-subtitle', 'Data siswa yang telah dinonaktifkan')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Arsip Siswa</h1>
        <p class="text-slate-500 mt-1 text-sm">Data siswa yang telah dinonaktifkan.</p>
    </div>

    <div class="mb-5 flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center flex-wrap gap-1 text-[13.5px]">
            <a href="#" id="back-btn" onclick="goBack(); return false;"
                class="inline-flex items-center gap-1 text-slate-400 hover:text-slate-600 font-medium py-0.5"
                style="display:none;">
                <i data-feather="chevron-left" class="w-4 h-4"></i> Kembali
            </a>
            <span id="back-sep" class="text-slate-300" style="display:none;">/</span>
            <div id="breadcrumb" class="flex items-center flex-wrap gap-1.5">
                <span class="font-semibold text-slate-900 cursor-default" data-level="root">Arsip Siswa</span>
            </div>
        </div>

        <div class="relative w-48 shrink-0">
            <i data-feather="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" id="arsip-search" placeholder="Cari siswa..."
                class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300">
        </div>
    </div>

    @if($arsipPerPeriode->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-10 sm:p-14 text-center">
            <i data-feather="archive" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <p class="text-slate-500 font-medium">Belum ada data siswa yang diarsipkan.</p>
        </div>
    @else

        <div class="arsip-explorer">

            <div id="level-periode">
                <div class="grid grid-cols-[repeat(auto-fill,minmax(210px,1fr))] gap-3">
                    @foreach($arsipPerPeriode as $tahunAjaran => $data)
                        <div class="periode-card group relative bg-white rounded-2xl border border-slate-200 p-5 cursor-pointer transition-all duration-200 hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5"
                            data-index="{{ $loop->index }}" data-label="{{ $tahunAjaran }}"
                            onclick="openPeriode({{ $loop->index }}, '{{ addslashes($tahunAjaran) }}')">

                            <div class="flex items-start justify-between mb-4">
                                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                                    <i data-feather="archive" class="w-5 h-5 text-blue-600"></i>
                                </div>
                                <i data-feather="chevron-right"
                                    class="w-4 h-4 text-slate-300 group-hover:text-blue-500 transition mt-1"></i>
                            </div>

                            <h3 class="text-base font-extrabold text-slate-900 leading-tight">{{ $tahunAjaran }}</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $data['kelas']->count() }} kelas &middot;
                                {{ $data['kelas']->flatten(1)->count() }} siswa
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            @foreach($arsipPerPeriode as $tahunAjaran => $data)
                <div id="level-kelas-{{ $loop->index }}" class="level-kelas" style="display:none;">
                    <div class="grid grid-cols-[repeat(auto-fill,minmax(210px,1fr))] gap-3">
                        @foreach($data['kelas'] as $kelas => $dataSiswa)
                            <div class="kelas-card group relative bg-white rounded-2xl border border-slate-200 p-5 cursor-pointer transition-all duration-200 hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5"
                                data-periode-index="{{ $loop->parent->index }}" data-index="{{ $loop->index }}"
                                data-label="{{ $kelas }}"
                                onclick="openKelas({{ $loop->parent->index }}, {{ $loop->index }}, '{{ addslashes($kelas) }}')">

                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                                        <i data-feather="users" class="w-5 h-5 text-blue-600"></i>
                                    </div>
                                    <i data-feather="chevron-right"
                                        class="w-4 h-4 text-slate-300 group-hover:text-blue-500 transition mt-1"></i>
                                </div>

                                <h3 class="text-base font-extrabold text-slate-900 leading-tight">Kelas {{ $kelas }}</h3>
                                <p class="text-xs text-slate-500 mt-1">{{ $dataSiswa->count() }} siswa</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach($data['kelas'] as $kelas => $dataSiswa)
                    <div id="level-siswa-{{ $loop->parent->index }}-{{ $loop->index }}" class="level-siswa" style="display:none;">
                        <div class="grid grid-cols-[repeat(auto-fill,minmax(160px,1fr))] gap-4">
                            @foreach($dataSiswa as $item)
                                <div class="member-card siswa-card group relative bg-white rounded-2xl border border-slate-200 overflow-hidden cursor-pointer shadow-sm transition-all duration-200 hover:border-blue-200 hover:shadow-md hover:-translate-y-0.5"
                                    data-nama="{{ strtolower($item->nama_siswa) }}" data-nis="{{ strtolower($item->nis) }}"
                                    onclick="window.location.href='{{ route('admin.arsip.show', $item->nis) }}'">

                                    <div class="relative aspect-square bg-slate-100">
                                        @if($item->foto)
                                            <img src="{{ route('foto.siswa', $item->nis) }}" alt="{{ $item->nama_siswa }}"
                                                class="absolute inset-0 w-full h-full object-cover"
                                                onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                            <div
                                                class="member-fallback hidden absolute inset-0 flex items-center justify-center text-4xl font-extrabold text-slate-300 bg-slate-100">
                                                {{ strtoupper(substr($item->nama_siswa, 0, 1)) }}
                                            </div>
                                        @else
                                            <div
                                                class="member-fallback absolute inset-0 flex items-center justify-center text-4xl font-extrabold text-slate-300 bg-slate-100">
                                                {{ strtoupper(substr($item->nama_siswa, 0, 1)) }}
                                            </div>
                                        @endif

                                        <div
                                            class="member-badge absolute top-2 left-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-600 border border-red-200">
                                            Arsip
                                        </div>

                                        <div class="member-actions absolute top-2 right-2 flex gap-1 opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                                            onclick="event.stopPropagation()">
                                            <a href="{{ route('admin.arsip.show', $item->nis) }}" title="Lihat Arsip"
                                                class="w-[26px] h-[26px] rounded-full flex items-center justify-center bg-white shadow border border-slate-100">
                                                <i data-feather="eye" class="w-[13px] h-[13px] text-slate-600"></i>
                                            </a>
                                            <form action="{{ route('admin.siswa.destroy', $item->nis) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Aktifkan Kembali"
                                                    onclick="return confirm('Aktifkan kembali akun siswa ini?')"
                                                    class="w-[26px] h-[26px] rounded-full flex items-center justify-center bg-white shadow border border-slate-100">
                                                    <i data-feather="refresh-cw" class="w-[13px] h-[13px] text-green-600"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="member-caption p-3">
                                        <div class="member-name text-slate-900 font-bold text-[13px] leading-tight truncate">
                                            {{ $item->nama_siswa }}
                                        </div>
                                        <div class="member-sub text-slate-500 text-[11px] mt-0.5 truncate">
                                            NIS {{ $item->nis }}
                                            @if($item->user?->nonaktif_at)
                                                &middot; {{ $item->user->nonaktif_at->format('d-m-Y') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endforeach

            <div id="level-search" style="display:none;">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2.5">Hasil Pencarian</p>
                <div id="level-search-grid" class="grid grid-cols-[repeat(auto-fill,minmax(160px,1fr))] gap-4"></div>
                <div id="search-empty" class="hidden bg-white rounded-2xl border border-slate-200 p-10 text-center">
                    <i data-feather="search" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                    <p class="text-slate-500 font-medium">Tidak ada siswa yang cocok.</p>
                </div>
            </div>

        </div>
    @endif

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        const arsipData = @json($arsipJson);

        let currentState = { level: 'root' };

        function refreshIcons() {
            if (typeof feather !== 'undefined') {
                feather.replace({ width: 16, height: 16 });
            }
        }

        function hideAllLevels() {
            document.getElementById('level-periode').style.display = 'none';
            document.querySelectorAll('.level-kelas, .level-siswa').forEach(el => el.style.display = 'none');
            document.getElementById('level-search').style.display = 'none';
            document.getElementById('search-empty').classList.add('hidden');
        }

        function toggleBackNav(show) {
            document.getElementById('back-btn').style.display = show ? '' : 'none';
            document.getElementById('back-sep').style.display = show ? '' : 'none';
        }

        const BREADCRUMB_ACTIVE = 'font-semibold text-slate-900 cursor-default';
        const BREADCRUMB_LINK = 'font-semibold text-slate-500 hover:text-slate-900 hover:underline cursor-pointer';

        function setBreadcrumb(items) {
            const wrap = document.getElementById('breadcrumb');
            wrap.innerHTML = '';
            items.forEach((item, idx) => {
                const isActive = idx === items.length - 1;
                const span = document.createElement('span');
                span.textContent = item.label;
                span.className = isActive ? BREADCRUMB_ACTIVE : BREADCRUMB_LINK;
                if (!isActive) span.onclick = item.onClick;
                wrap.appendChild(span);
                if (idx < items.length - 1) {
                    const sep = document.createElement('span');
                    sep.textContent = '/';
                    sep.className = 'text-slate-300';
                    wrap.appendChild(sep);
                }
            });

            toggleBackNav(items.length > 1);
            refreshIcons();
        }

        function goToRoot() {
            document.getElementById('arsip-search').value = '';
            hideAllLevels();
            document.getElementById('level-periode').style.display = '';
            currentState = { level: 'root' };
            setBreadcrumb([{ label: 'Arsip Siswa' }]);
        }

        function openPeriode(periodeIndex, periodeLabel) {
            hideAllLevels();
            document.getElementById('level-kelas-' + periodeIndex).style.display = '';
            currentState = { level: 'periode', periodeIndex, periodeLabel };
            setBreadcrumb([
                { label: 'Arsip Siswa', onClick: goToRoot },
                { label: periodeLabel },
            ]);
        }

        function openKelas(periodeIndex, kelasIndex, kelasLabel) {
            hideAllLevels();
            document.getElementById('level-siswa-' + periodeIndex + '-' + kelasIndex).style.display = '';
            const periodeCard = document.querySelector('.periode-card[data-index="' + periodeIndex + '"]');
            const periodeLabel = periodeCard ? periodeCard.dataset.label : currentState.periodeLabel;
            currentState = { level: 'kelas', periodeIndex, kelasIndex, periodeLabel, kelasLabel };
            setBreadcrumb([
                { label: 'Arsip Siswa', onClick: goToRoot },
                { label: periodeLabel, onClick: () => openPeriode(periodeIndex, periodeLabel) },
                { label: 'Kelas ' + kelasLabel },
            ]);
        }

        function goBack() {
            if (currentState.level === 'kelas') {
                openPeriode(currentState.periodeIndex, currentState.periodeLabel);
            } else {
                goToRoot();
            }
        }

        function buildMemberCardHTML(s) {
            const fotoHtml = s.foto_url
                ? `<img src="${s.foto_url}" alt="${s.nama}" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                           <div class="member-fallback hidden absolute inset-0 flex items-center justify-center text-4xl font-extrabold text-slate-300 bg-slate-100">${s.nama.charAt(0).toUpperCase()}</div>`
                : `<div class="member-fallback absolute inset-0 flex items-center justify-center text-4xl font-extrabold text-slate-300 bg-slate-100">${s.nama.charAt(0).toUpperCase()}</div>`;

            return `
                        <div class="relative aspect-square bg-slate-100">
                            ${fotoHtml}
                            <div class="absolute top-2 left-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-600 border border-red-200">Arsip</div>
                        </div>
                        <div class="p-3">
                            <div class="text-slate-900 font-bold text-[13px] leading-tight truncate">${s.nama}</div>
                            <div class="text-slate-500 text-[11px] mt-0.5 truncate">NIS ${s.nis} &middot; ${s.kelas}${s.nonaktif_at ? ' &middot; ' + s.nonaktif_at : ''}</div>
                        </div>
                    `;
        }

        function openFromQueryString() {
            const params = new URLSearchParams(window.location.search);
            const qPeriode = params.get('periode');
            const qKelas = params.get('kelas');

            if (!qPeriode) return false;

            const periodeCard = document.querySelector('.periode-card[data-label="' + CSS.escape(qPeriode) + '"]');
            if (!periodeCard) return false;
            const periodeIndex = periodeCard.dataset.index;

            if (!qKelas) {
                openPeriode(periodeIndex, qPeriode);
                if (window.history.replaceState) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
                return true;
            }

            const kelasCard = document.querySelector(
                '.kelas-card[data-periode-index="' + periodeIndex + '"][data-label="' + CSS.escape(qKelas) + '"]'
            );

            if (!kelasCard) {
                openPeriode(periodeIndex, qPeriode);
            } else {
                openKelas(periodeIndex, kelasCard.dataset.index, qKelas);
            }

            if (window.history.replaceState) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            return true;
        }

        document.addEventListener('DOMContentLoaded', function () {
            refreshIcons();

            if (!openFromQueryString()) {
                goToRoot();
            }

            window.addEventListener('pageshow', function (event) {
                if (event.persisted) {
                    if (!openFromQueryString()) {
                        goToRoot();
                    }
                }
            });

            const searchInput = document.getElementById('arsip-search');
            const searchWrap = document.getElementById('level-search');
            const searchGrid = document.getElementById('level-search-grid');
            const searchEmpty = document.getElementById('search-empty');

            searchInput.addEventListener('input', function () {
                const keyword = this.value.trim().toLowerCase();

                if (!keyword) {
                    goToRoot();
                    return;
                }

                hideAllLevels();
                searchWrap.style.display = '';
                currentState = { level: 'search' };
                setBreadcrumb([
                    { label: 'Arsip Siswa', onClick: goToRoot },
                    { label: 'Hasil pencarian: "' + this.value.trim() + '"' },
                ]);

                const hasil = arsipData.filter(s =>
                    s.nama.toLowerCase().includes(keyword) || s.nis.toLowerCase().includes(keyword)
                );

                searchGrid.innerHTML = '';
                searchEmpty.classList.toggle('hidden', hasil.length !== 0);

                hasil.forEach(s => {
                    const div = document.createElement('div');
                    div.className = 'bg-white rounded-2xl border border-slate-200 overflow-hidden cursor-pointer shadow-sm transition-all duration-200 hover:border-blue-200 hover:shadow-md hover:-translate-y-0.5';
                    div.onclick = () => window.location.href = s.show_url;
                    div.innerHTML = buildMemberCardHTML(s);
                    searchGrid.appendChild(div);
                });

                refreshIcons();
            });
        });
    </script>

@endsection