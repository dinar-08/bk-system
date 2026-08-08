@extends('layouts.admin')

@section('title', 'Arsip Siswa')
@section('page-title', 'Arsip Siswa')
@section('page-subtitle', 'Data siswa yang telah dinonaktifkan')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Arsip Siswa</h1>
        <p class="text-slate-500 mt-1 text-sm">Data siswa yang telah dinonaktifkan.</p>
    </div>

    {{-- Navigasi: tombol kembali + breadcrumb + search --}}
    <div class="mb-5 flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center flex-wrap gap-1 text-[13.5px]">
            <button type="button" id="back-btn"
                class="inline-flex items-center gap-0.5 font-semibold text-slate-500 hover:text-slate-900 py-0.5"
                style="display:none;" onclick="goBack()">
                <i data-feather="chevron-left" class="w-[15px] h-[15px]"></i> Kembali
            </button>
            <span id="back-sep" class="text-slate-300" style="display:none;">/</span>
            <div id="breadcrumb" class="flex items-center flex-wrap gap-1.5">
                <span class="font-semibold text-slate-900 cursor-default" data-level="root">Arsip Siswa</span>
            </div>
        </div>

        <div class="relative w-48 shrink-0">
            <input type="text" id="arsip-search" placeholder="Cari siswa..."
                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-slate-800/20">
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
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2.5">Pilih Tahun Ajaran</p>
                <div class="flex flex-nowrap overflow-x-auto pb-3">
                    @foreach($arsipPerPeriode as $tahunAjaran => $data)
                        <div class="spine-card spine-periode group relative shrink-0 h-[240px] border-r-4 border-white bg-gradient-to-b from-slate-400 via-slate-600 to-slate-900 overflow-hidden cursor-pointer transition-all duration-500 ease-out"
                            style="width: 56px;" onmouseenter="this.style.width='260px'" onmouseleave="this.style.width='56px'"
                            onclick="openPeriode({{ $loop->index }}, '{{ addslashes($tahunAjaran) }}')">

                            <div class="absolute inset-0 bg-gradient-to-b from-black/5 via-black/10 to-black/65"></div>

                            <p
                                class="spine-label absolute left-4 bottom-4 max-w-[210px] text-white text-xs font-extrabold tracking-[2px] uppercase whitespace-nowrap -rotate-90 origin-bottom-left transition-opacity duration-300 group-hover:opacity-0">
                                {{ $tahunAjaran }}
                            </p>

                            <div
                                class="absolute inset-0 p-[18px] flex flex-col justify-between opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                <i data-feather="archive" class="w-6 h-6 text-white/85"></i>
                                <div>
                                    <h3 class="text-white text-base font-extrabold leading-tight">{{ $tahunAjaran }}</h3>
                                    <p class="text-white/80 text-xs mt-1">
                                        {{ $data['kelas']->count() }} kelas &middot;
                                        {{ $data['kelas']->flatten(1)->count() }} siswa
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @foreach($arsipPerPeriode as $tahunAjaran => $data)
                <div id="level-kelas-{{ $loop->index }}" class="level-kelas" style="display:none;">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2.5">Pilih Kelas &middot;
                        {{ $tahunAjaran }}</p>
                    <div class="flex flex-nowrap overflow-x-auto pb-3">
                        @foreach($data['kelas'] as $kelas => $dataSiswa)
                            <div class="spine-card spine-kelas group relative shrink-0 h-[240px] border-r-4 border-white bg-gradient-to-b from-slate-300 via-slate-500 to-slate-800 overflow-hidden cursor-pointer transition-all duration-500 ease-out"
                                style="width: 56px;" onmouseenter="this.style.width='260px'" onmouseleave="this.style.width='56px'"
                                onclick="openKelas({{ $loop->parent->index }}, {{ $loop->index }}, '{{ addslashes($kelas) }}')">

                                <div class="absolute inset-0 bg-gradient-to-b from-black/5 via-black/10 to-black/65"></div>

                                <p
                                    class="spine-label absolute left-4 bottom-4 max-w-[210px] text-white text-xs font-extrabold tracking-[2px] uppercase whitespace-nowrap -rotate-90 origin-bottom-left transition-opacity duration-300 group-hover:opacity-0">
                                    Kelas {{ $kelas }}
                                </p>

                                <div
                                    class="absolute inset-0 p-[18px] flex flex-col justify-between opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    <i data-feather="users" class="w-6 h-6 text-white/85"></i>
                                    <div>
                                        <h3 class="text-white text-base font-extrabold leading-tight">Kelas {{ $kelas }}</h3>
                                        <p class="text-white/80 text-xs mt-1">{{ $dataSiswa->count() }} siswa</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach($data['kelas'] as $kelas => $dataSiswa)
                    <div id="level-siswa-{{ $loop->parent->index }}-{{ $loop->index }}" class="level-siswa" style="display:none;">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2.5">Siswa &middot; Kelas {{ $kelas }}
                            &middot; {{ $tahunAjaran }}</p>
                        <div class="grid grid-cols-[repeat(auto-fill,minmax(150px,1fr))] gap-[18px]">
                            @foreach($dataSiswa as $item)
                                <div class="member-card siswa-card group relative aspect-[3/4] rounded-2xl overflow-hidden cursor-pointer bg-slate-200 border-[3px] border-slate-500 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                                    data-nama="{{ strtolower($item->nama_siswa) }}" data-nis="{{ strtolower($item->nis) }}"
                                    onclick="window.location.href='{{ route('admin.arsip.show', $item->nis) }}'">

                                    @if($item->foto)
                                        <img src="{{ route('foto.siswa', $item->nis) }}" alt="{{ $item->nama_siswa }}"
                                            class="absolute inset-0 w-full h-full object-cover grayscale-[35%]"
                                            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                        <div
                                            class="member-fallback hidden absolute inset-0 flex items-center justify-center font-extrabold text-4xl text-white bg-gradient-to-b from-slate-400 via-slate-600 to-slate-900">
                                            {{ strtoupper(substr($item->nama_siswa, 0, 1)) }}
                                        </div>
                                    @else
                                        <div
                                            class="member-fallback absolute inset-0 flex items-center justify-center font-extrabold text-4xl text-white bg-gradient-to-b from-slate-400 via-slate-600 to-slate-900">
                                            {{ strtoupper(substr($item->nama_siswa, 0, 1)) }}
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/10 to-transparent"></div>

                                    <div
                                        class="member-badge absolute top-2 left-2 text-[9.5px] font-extrabold text-slate-100 bg-slate-500/40 border border-slate-300/40 px-2 py-0.5 rounded-full">
                                        Nonaktif
                                    </div>

                                    <div class="member-actions absolute top-2 right-2 flex gap-1 opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                                        onclick="event.stopPropagation()">
                                        <a href="{{ route('admin.arsip.show', $item->nis) }}" title="Lihat Arsip"
                                            class="w-[26px] h-[26px] rounded-full flex items-center justify-center bg-white/90 shadow">
                                            <i data-feather="eye" class="w-[13px] h-[13px] text-slate-700"></i>
                                        </a>
                                        <form action="{{ route('admin.siswa.destroy', $item->nis) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Aktifkan Kembali"
                                                onclick="return confirm('Aktifkan kembali akun siswa ini?')"
                                                class="w-[26px] h-[26px] rounded-full flex items-center justify-center bg-white/90 shadow">
                                                <i data-feather="refresh-cw" class="w-[13px] h-[13px] text-green-600"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <div class="member-caption absolute inset-x-0 bottom-0 p-3">
                                        <div class="member-name text-white font-extrabold text-[13px] leading-tight">{{ $item->nama_siswa }}
                                        </div>
                                        <div class="member-sub text-white/75 text-[11px] mt-0.5">
                                            NIS {{ $item->nis }}
                                            @if($item->user?->nonaktif_at)
                                                &middot; Nonaktif {{ $item->user->nonaktif_at->format('d-m-Y') }}
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
                <div id="level-search-grid" class="grid grid-cols-[repeat(auto-fill,minmax(150px,1fr))] gap-[18px]"></div>
                <p id="search-empty" class="hidden text-center text-slate-500 py-8">Tidak ada siswa yang cocok.</p>
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
        const BREADCRUMB_LINK = 'font-semibold text-slate-600 hover:text-slate-900 hover:underline cursor-pointer';

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
            const periodeLabelEl = document.querySelectorAll('.spine-periode .spine-label')[periodeIndex];
            const periodeLabel = periodeLabelEl ? periodeLabelEl.textContent.trim() : currentState.periodeLabel;
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
                ? `<img src="${s.foto_url}" alt="${s.nama}" class="absolute inset-0 w-full h-full object-cover grayscale-[35%]" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                       <div class="member-fallback hidden absolute inset-0 flex items-center justify-center font-extrabold text-4xl text-white bg-gradient-to-b from-slate-400 via-slate-600 to-slate-900">${s.nama.charAt(0).toUpperCase()}</div>`
                : `<div class="member-fallback absolute inset-0 flex items-center justify-center font-extrabold text-4xl text-white bg-gradient-to-b from-slate-400 via-slate-600 to-slate-900">${s.nama.charAt(0).toUpperCase()}</div>`;

            return `
                    ${fotoHtml}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/10 to-transparent"></div>
                    <div class="absolute top-2 left-2 text-[9.5px] font-extrabold text-slate-100 bg-slate-500/40 border border-slate-300/40 px-2 py-0.5 rounded-full">Nonaktif</div>
                    <div class="absolute inset-x-0 bottom-0 p-3">
                        <div class="text-white font-extrabold text-[13px] leading-tight">${s.nama}</div>
                        <div class="text-white/75 text-[11px] mt-0.5">NIS ${s.nis} &middot; ${s.kelas}${s.nonaktif_at ? ' &middot; Nonaktif ' + s.nonaktif_at : ''}</div>
                    </div>
                `;
        }

        function openFromQueryString() {
            const params = new URLSearchParams(window.location.search);
            const qPeriode = params.get('periode');
            const qKelas = params.get('kelas');

            if (!qPeriode) return false;

            const periodeLabels = document.querySelectorAll('.spine-periode .spine-label');
            let periodeIndex = -1;
            periodeLabels.forEach((el, idx) => {
                if (el.textContent.trim() === qPeriode) periodeIndex = idx;
            });
            if (periodeIndex === -1) return false;

            if (!qKelas) {
                openPeriode(periodeIndex, qPeriode);
                if (window.history.replaceState) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
                return true;
            }

            const kelasContainer = document.getElementById('level-kelas-' + periodeIndex);
            const kelasLabels = kelasContainer.querySelectorAll('.spine-kelas .spine-label');
            let kelasIndex = -1;
            kelasLabels.forEach((el, idx) => {
                if (el.textContent.trim().toLowerCase() === ('Kelas ' + qKelas).toLowerCase()) kelasIndex = idx;
            });

            if (kelasIndex === -1) {
                openPeriode(periodeIndex, qPeriode);
            } else {
                openKelas(periodeIndex, kelasIndex, qKelas);
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
                    div.className = 'group relative aspect-[3/4] rounded-2xl overflow-hidden cursor-pointer bg-slate-200 border-[3px] border-slate-500 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl';
                    div.onclick = () => window.location.href = s.show_url;
                    div.innerHTML = buildMemberCardHTML(s);
                    searchGrid.appendChild(div);
                });

                refreshIcons();
            });
        });
    </script>

@endsection