<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dokumen yang Dibagikan
            </h2>
            <p class="text-sm text-gray-500">
                Daftar dokumen/template/proposal file yang dibagikan kepada Anda. Gunakan pencarian untuk cepat menemukan item.
            </p>
        </div>
    </x-slot>

    @php
        $roleLabelMap = [
            'super_admin'            => 'Super Admin',
            'ketua'                  => 'Ketua',
            'wakil_ketua'            => 'Wakil Ketua',
            'sekretaris'             => 'Sekretaris',
            'bendahara'              => 'Bendahara',
            'ketua_bidang'           => 'Ketua Bidang',
            'ketua_sie'              => 'Ketua Sie',
            'ketua_lingkungan'       => 'Ketua Lingkungan',
            'wakil_ketua_lingkungan' => 'Wakil Ketua Lingkungan',
            'anggota_komunitas'      => 'Anggota Komunitas',
        ];

        // Kalau kamu tidak kirim $bidangs ke view sharedIndex, chip bidang akan tampil "Bidang #ID"
        // (Bisa kamu tambah di controller kalau mau, tapi versi ini tetap aman)
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- TOP BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="text-sm text-gray-600">
                        Total: <span class="font-semibold text-gray-900">{{ $items->count() }}</span> item
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        <div class="relative">
                            <input
                                id="sharedSearch"
                                type="text"
                                placeholder="Cari judul / nama file / tipe..."
                                class="w-full sm:w-80 pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            >
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 21l-4.3-4.3"/>
                                    <circle cx="11" cy="11" r="7"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    @if($items->isEmpty())
                        <div class="text-center py-10">
                            <div class="text-gray-900 font-semibold">Belum ada dokumen yang dibagikan</div>
                            <div class="text-sm text-gray-600 mt-1">Jika ada dokumen dibagikan, akan muncul di sini.</div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                        <th class="px-4 py-3">Sumber</th>
                                        <th class="px-4 py-3">Judul / Nama</th>
                                        <th class="px-4 py-3">Dibagikan lewat</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody id="sharedTableBody" class="divide-y">
                                    @foreach($items as $item)
                                        @php
                                            $sourceLabel = class_basename($item->source_type);
                                            $source = $item->source;

                                            $titleForSearch = '';
                                            if ($sourceLabel === 'Template') {
                                                $titleForSearch = ($source?->title ?? '').' '.($source?->original_name ?? '');
                                            } elseif ($sourceLabel === 'ProposalFile') {
                                                $titleForSearch = ($source?->original_name ?? '').' '.($source?->mime_type ?? '');
                                            } elseif ($sourceLabel === 'Document') {
                                                $titleForSearch = ($source?->title ?? '').' '.($source?->filename ?? '');
                                            }

                                            $rolesNice = collect($item->shared_roles ?? [])
                                                ->map(fn($r) => $roleLabelMap[$r] ?? $r)
                                                ->filter()
                                                ->values();

                                            $bidangsNice = collect($item->shared_bidang_ids ?? [])
                                                ->map(fn($id) => 'Bidang #'.$id)  // aman tanpa query DB
                                                ->values();

                                            $searchText = strtolower(
                                                $sourceLabel.' '.$titleForSearch.' '.
                                                implode(' ', $item->shared_roles ?? []).' '.
                                                implode(' ', array_map('strval', $item->shared_bidang_ids ?? []))
                                            );
                                        @endphp

                                        <tr class="hover:bg-gray-50 align-top shared-row"
                                            data-search="{{ $searchText }}">

                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-50 text-blue-700">
                                                    {{ $sourceLabel }}
                                                </span>
                                            </td>

                                            <td class="px-4 py-3">
                                                @if($sourceLabel === 'Template')
                                                    <div class="font-semibold text-gray-900">
                                                        {{ $source?->title ?? '(Template hilang)' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $source?->original_name ?? '-' }}
                                                    </div>

                                                @elseif($sourceLabel === 'ProposalFile')
                                                    <div class="font-semibold text-gray-900">
                                                        {{ $source?->original_name ?? '(File proposal hilang)' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $source?->mime_type ?? '-' }}
                                                    </div>

                                                @elseif($sourceLabel === 'Document')
                                                    <div class="font-semibold text-gray-900">
                                                        {{ $source?->title ?? '(Dokumen hilang)' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $source?->filename ?? '-' }}
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-500">
                                                        Tipe tidak dikenali: {{ $sourceLabel }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="space-y-2">
                                                    <div>
                                                        <div class="text-xs text-gray-500 mb-1">Roles</div>
                                                        @if($rolesNice->isEmpty())
                                                            <span class="text-xs text-gray-500">-</span>
                                                        @else
                                                            <div class="flex flex-wrap gap-1">
                                                                @foreach($rolesNice as $rn)
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                                                        {{ $rn }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div>
                                                        <div class="text-xs text-gray-500 mb-1">Bidang</div>
                                                        @if($bidangsNice->isEmpty())
                                                            <span class="text-xs text-gray-500">-</span>
                                                        @else
                                                            <div class="flex flex-wrap gap-1">
                                                                @foreach($bidangsNice as $bn)
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-50 text-blue-700">
                                                                        {{ $bn }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center gap-2 flex-wrap">

                                                    {{-- Template --}}
                                                    @if($sourceLabel === 'Template' && $source)
                                                        @if(strtolower($source->file_type) === 'pdf')
                                                            <a href="{{ route('templates.stream', $source) }}" target="_blank"
                                                               class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                                Preview
                                                            </a>
                                                        @else
                                                            <a href="{{ route('templates.view', $source) }}" target="_blank"
                                                               class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                                Preview
                                                            </a>
                                                        @endif

                                                        <a href="{{ route('templates.download', $source) }}"
                                                           class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                            Download
                                                        </a>
                                                    @endif

                                                    {{-- ProposalFile (tanpa program) --}}
                                                    @if($sourceLabel === 'ProposalFile' && $source)
                                                        @php
                                                            $source->loadMissing('proposal');
                                                            $proposalId = $source->proposal_id;
                                                            $fileId = $source->id;
                                                        @endphp

                                                        @if($proposalId && $fileId)
                                                            <a href="{{ route('proposals.files.preview', ['proposal' => $proposalId, 'file' => $fileId]) }}" target="_blank"
                                                               class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                                Preview
                                                            </a>

                                                            <a href="{{ route('proposals.files.download', ['proposal' => $proposalId, 'file' => $fileId]) }}"
                                                               class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                                Download
                                                            </a>
                                                        @else
                                                            <span class="text-xs text-gray-500">Lampiran proposal tidak valid.</span>
                                                        @endif
                                                    @endif

                                                    {{-- Document: Preview + Download (KONSISTEN) --}}
                                                    @if($sourceLabel === 'Document' && $source)
                                                        <a href="{{ route('documents.preview', $source) }}" target="_blank"
                                                        class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                            Preview
                                                        </a>

                                                        <a href="{{ route('documents.download', $source) }}"
                                                        class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                            Download
                                                        </a>
                                                    @endif

                                                    @if(!$source)
                                                        <span class="text-xs text-gray-500">Sumber dokumen sudah tidak ada.</span>
                                                    @endif
                                                </div>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div id="noSharedResult" class="hidden text-center py-10">
                            <div class="text-gray-900 font-semibold">Item tidak ditemukan</div>
                            <div class="text-sm text-gray-600 mt-1">Coba kata kunci lain.</div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @once
        <script>
            const sharedSearch = document.getElementById('sharedSearch');
            const sharedRows = document.querySelectorAll('.shared-row');
            const noSharedResult = document.getElementById('noSharedResult');

            if (sharedSearch) {
                sharedSearch.addEventListener('input', () => {
                    const q = (sharedSearch.value || '').toLowerCase().trim();
                    let visible = 0;

                    sharedRows.forEach(row => {
                        const hay = row.getAttribute('data-search') || '';
                        const show = hay.includes(q);
                        row.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });

                    if (noSharedResult) noSharedResult.classList.toggle('hidden', visible !== 0);
                });
            }
        </script>
    @endonce
</x-app-layout>
