<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengumuman</h2>
            <p class="text-sm text-gray-500">
                Informasi rapat, kegiatan, dan berita internal gereja.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- TOP BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="text-sm text-gray-600">
                        Total tampil: <span class="font-semibold text-gray-900" id="annTotal">{{ $items->count() }}</span>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        {{-- Search (LIVE) --}}
                        <div class="relative">
                            <input
                                id="annSearch"
                                type="text"
                                value="{{ $q }}"
                                placeholder="Cari judul / isi / pembuat / target..."
                                class="w-full sm:w-80 pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            >
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 21l-4.3-4.3"/>
                                    <circle cx="11" cy="11" r="7"/>
                                </svg>
                            </span>
                        </div>

                        @if($canManage)
                            <a href="{{ route('announcements.create') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium">
                                <span class="text-lg leading-none">＋</span>
                                Buat Pengumuman
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- LIST CARD (ramah lansia) --}}
            <div class="space-y-3">
                @if($items->isEmpty())
                    <div class="bg-white shadow sm:rounded-lg p-6 text-center">
                        <div class="text-gray-900 font-semibold">Belum ada pengumuman</div>
                        <div class="text-sm text-gray-600 mt-1">Jika ada rapat/kegiatan, pengumuman akan muncul di sini.</div>
                    </div>
                @else
                    @foreach($items as $a)
                        @php
                            $tk = $a->target_kedudukan ?? [];
                            $tr = $a->target_roles ?? [];
                            $targetText = (empty($tk) && empty($tr)) ? 'semua' : ('kedudukan '.implode(' ', $tk).' roles '.implode(' ', $tr));
                            $hay = strtolower(trim(
                                ($a->title ?? '').' '.
                                ($a->body ?? '').' '.
                                (($a->creator?->name) ?? '').' '.
                                $targetText
                            ));
                        @endphp

                        <a href="{{ route('announcements.show', $a) }}"
                           class="block bg-white shadow sm:rounded-lg p-5 hover:bg-gray-50 transition ann-row"
                           data-search="{{ $hay }}">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <div class="text-lg font-semibold text-gray-900 truncate">
                                            {{ $a->title }}
                                        </div>

                                        @if($a->is_pinned)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-50 text-yellow-800">
                                                Dipin
                                            </span>
                                        @endif

                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                            {{ $a->status === 'published' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $a->status === 'published' ? 'Published' : 'Draft' }}
                                        </span>
                                    </div>

                                    <div class="text-sm text-gray-600 mt-1 whitespace-pre-line">
                                        {{ \Illuminate\Support\Str::limit($a->body, 160) }}
                                    </div>

                                    <div class="text-xs text-gray-500 mt-2">
                                        Oleh: {{ $a->creator?->name ?? '-' }} • {{ optional($a->created_at)->format('d-m-Y | H:i') }}
                                    </div>
                                </div>

                                <div class="text-xs text-gray-500 sm:text-right">
                                    <div>
                                        Target:
                                        <span class="font-medium text-gray-700">
                                            {{ (empty($tk) && empty($tr)) ? 'Semua' : '' }}
                                        </span>
                                    </div>
                                    @if(!empty($tk))
                                        <div class="mt-1">
                                            Kedudukan: <span class="text-gray-700">{{ implode(', ', $tk) }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($tr))
                                        <div class="mt-1">
                                            Role: <span class="text-gray-700">{{ implode(', ', $tr) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach

                    {{-- no-result --}}
                    <div id="annNoResult" class="hidden bg-white shadow sm:rounded-lg p-6 text-center">
                        <div class="text-gray-900 font-semibold">Pengumuman tidak ditemukan</div>
                        <div class="text-sm text-gray-600 mt-1">Coba kata kunci lain.</div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- LIVE SEARCH SCRIPT (mirip documents) --}}
    @once
        <script>
            const annSearch = document.getElementById('annSearch');
            const annRows = document.querySelectorAll('.ann-row');
            const annNoResult = document.getElementById('annNoResult');
            const annTotal = document.getElementById('annTotal');

            function runAnnFilter() {
                const q = (annSearch?.value || '').toLowerCase().trim();
                let visible = 0;

                annRows.forEach(row => {
                    const hay = row.getAttribute('data-search') || '';
                    const show = hay.includes(q);
                    row.classList.toggle('hidden', !show);
                    if (show) visible++;
                });

                if (annNoResult) annNoResult.classList.toggle('hidden', visible !== 0);
                if (annTotal) annTotal.textContent = visible;
            }

            if (annSearch) {
                annSearch.addEventListener('input', runAnnFilter);

                // kalau sebelumnya q terisi dari server, langsung filter saat load
                if ((annSearch.value || '').trim() !== '') {
                    runAnnFilter();
                }
            }
        </script>
    @endonce
</x-app-layout>
