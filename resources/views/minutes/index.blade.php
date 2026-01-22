<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notulensi Rapat</h2>
            <p class="text-sm text-gray-500">
                Lihat notulensi rapat internal. Gunakan pencarian untuk menemukan notulensi dengan cepat.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- TOP BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="text-sm text-gray-600">
                        Total: <span class="font-semibold text-gray-900" id="minuteTotal">{{ $minutes->count() }}</span> notulensi
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        {{-- Search (LIVE) --}}
                        <div class="relative">
                            <input
                                id="minuteSearch"
                                type="text"
                                placeholder="Cari judul / lokasi / pembuat / agenda..."
                                class="w-full sm:w-80 pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            >
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 21l-4.3-4.3"/>
                                    <circle cx="11" cy="11" r="7"/>
                                </svg>
                            </span>
                        </div>

                        @if(auth()->user()->canManageMinutes())
                            <a href="{{ route('minutes.create') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium">
                                <span class="text-lg leading-none">＋</span>
                                Buat Notulensi
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    @if ($minutes->isEmpty())
                        <div class="text-center py-10">
                            <div class="text-gray-900 font-semibold">Belum ada notulensi</div>
                            <div class="text-sm text-gray-600 mt-1">Notulensi rapat akan muncul di sini.</div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                        <th class="px-4 py-3">Notulensi</th>
                                        <th class="px-4 py-3">Tanggal Rapat</th>
                                        <th class="px-4 py-3">Pembuat</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody id="minuteTableBody" class="divide-y">
                                    @foreach ($minutes as $m)
                                        @php
                                            $hay = strtolower(trim(
                                                ($m->title ?? '').' '.
                                                ($m->location ?? '').' '.
                                                ($m->agenda ?? '').' '.
                                                (($m->creator?->name) ?? '').' '.
                                                ($m->status ?? '').' '.
                                                (optional($m->meeting_at)->format('d-m-Y').' '.optional($m->meeting_at)->format('H:i'))
                                            ));
                                        @endphp

                                        <tr class="hover:bg-gray-50 minute-row" data-search="{{ $hay }}">
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-gray-900">{{ $m->title }}</div>

                                                @if($m->location)
                                                    <div class="text-xs text-gray-500 mt-1">📍 {{ $m->location }}</div>
                                                @endif

                                                @if($m->agenda)
                                                    <div class="text-xs text-gray-600 mt-1">
                                                        {{ \Illuminate\Support\Str::limit($m->agenda, 70) }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">{{ $m->meeting_at->format('d-m-Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $m->meeting_at->format('H:i') }}</div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">{{ $m->creator?->name ?? '-' }}</div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                                    {{ $m->status === 'published' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                                    {{ $m->status === 'published' ? 'Published' : 'Draft' }}
                                                </span>
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                                    <a href="{{ route('minutes.show', $m) }}"
                                                       class="px-3 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 text-sm">
                                                        Detail
                                                    </a>

                                                    @if(auth()->user()->canManageMinutes())
                                                        <a href="{{ route('minutes.edit', $m) }}"
                                                           class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                            Edit
                                                        </a>

                                                        <form id="delete-minute-{{ $m->id }}" method="POST"
                                                              action="{{ route('minutes.destroy', $m) }}" class="hidden">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>

                                                        <button type="button"
                                                                onclick="confirmDeleteMinute({{ $m->id }})"
                                                                class="px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
                                                            Hapus
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- no-result --}}
                        <div id="minuteNoResult" class="hidden text-center py-10">
                            <div class="text-gray-900 font-semibold">Notulensi tidak ditemukan</div>
                            <div class="text-sm text-gray-600 mt-1">Coba kata kunci lain.</div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @once
        <script>
            // Live Search (client-side) - konsisten dengan Documents
            const minuteSearch = document.getElementById('minuteSearch');
            const minuteRows = document.querySelectorAll('.minute-row');
            const minuteNoResult = document.getElementById('minuteNoResult');
            const minuteTotal = document.getElementById('minuteTotal');

            if (minuteSearch) {
                minuteSearch.addEventListener('input', () => {
                    const q = (minuteSearch.value || '').toLowerCase().trim();
                    let visible = 0;

                    minuteRows.forEach(row => {
                        const hay = row.getAttribute('data-search') || '';
                        const show = hay.includes(q);
                        row.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });

                    if (minuteNoResult) minuteNoResult.classList.toggle('hidden', visible !== 0);
                    if (minuteTotal) minuteTotal.textContent = visible;
                });
            }

            function confirmDeleteMinute(id) {
                Swal.fire({
                    title: 'Hapus notulensi?',
                    text: "Notulensi yang dihapus tidak bisa dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-minute-' + id);
                        if (form) form.submit();
                    }
                });
            }
            window.confirmDeleteMinute = confirmDeleteMinute;
        </script>
    @endonce
</x-app-layout>
