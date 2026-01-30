<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Folder: {{ $folder->name }}
            </h2>
            <p class="text-sm text-gray-500">
                Dokumen yang tersimpan di folder ini bisa diatur menjadi private / shared dan dibagikan ke role/bidang.
            </p>
        </div>
    </x-slot>

    @php
        // Label roles biar "etis" dan enak dibaca (bukan nama DB)
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

        // Map bidang id -> nama_bidang untuk tampilan chip
        $bidangNameMap = $bidangs->pluck('nama_bidang', 'id');
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- TOP BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('folders.index') }}"
                           class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm text-gray-800">
                            ← Kembali
                        </a>
                        <div class="text-sm text-gray-600">
                            Total: <span class="font-semibold text-gray-900">{{ $folder->items->count() }}</span> item
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        <div class="relative">
                            <input
                                id="itemSearch"
                                type="text"
                                placeholder="Cari judul / nama file..."
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

            {{-- LIST --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Dokumen di Folder</h3>

                    @if($folder->items->isEmpty())
                        <div class="text-center py-10">
                            <div class="text-gray-900 font-semibold">Belum ada dokumen di folder ini</div>
                            <div class="text-sm text-gray-600 mt-1">Tambahkan item dari menu Dokumen / Template / Proposal (tombol “+ Folder”).</div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                        <th class="px-4 py-3">Sumber</th>
                                        <th class="px-4 py-3">Judul / Nama</th>
                                        <th class="px-4 py-3">Mode</th>
                                        <th class="px-4 py-3">Share</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody id="itemTableBody" class="divide-y">
                                    @foreach($folder->items as $item)
                                        @php
                                            $sourceLabel = class_basename($item->source_type);
                                            $source = $item->source;

                                            // Search string
                                            $titleForSearch = '';
                                            if ($sourceLabel === 'Template') {
                                                $titleForSearch = ($source?->title ?? '').' '.($source?->original_name ?? '');
                                            } elseif ($sourceLabel === 'ProposalFile') {
                                                $titleForSearch = ($source?->original_name ?? '').' '.($source?->mime_type ?? '');
                                            } elseif ($sourceLabel === 'Document') {
                                                $titleForSearch = ($source?->title ?? '').' '.($source?->filename ?? '');
                                            }

                                            // Nice share labels
                                            $selectedRoles = $item->shared_roles ?? [];
                                            $rolesNice = collect($selectedRoles)
                                                ->map(fn($r) => $roleLabelMap[$r] ?? $r)
                                                ->filter()
                                                ->values();

                                            $selectedBidangIds = $item->shared_bidang_ids ?? [];
                                            $bidangsNice = collect($selectedBidangIds)
                                                ->map(fn($id) => $bidangNameMap[(int)$id] ?? ('Bidang #'.$id))
                                                ->filter()
                                                ->values();
                                        @endphp

                                        <tr class="hover:bg-gray-50 align-top item-row"
                                            data-search="{{ strtolower($sourceLabel.' '.$titleForSearch) }}">
                                            {{-- Sumber --}}
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-50 text-blue-700">
                                                    {{ $sourceLabel }}
                                                </span>
                                            </td>

                                            {{-- Judul / Nama --}}
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

                                            {{-- Mode --}}
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                                    {{ $item->is_private ? 'bg-gray-100 text-gray-700' : 'bg-green-50 text-green-700' }}">
                                                    {{ $item->is_private ? 'Private' : 'Shared' }}
                                                </span>
                                            </td>

                                            {{-- Share (rapi pakai chip) --}}
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                @if($item->is_private)
                                                    <span class="text-gray-500">—</span>
                                                @else
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
                                                @endif
                                            </td>

                                            {{-- Aksi (konsisten: Preview, Download, Setting, Hapus) --}}
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center gap-2 flex-wrap">

                                                    {{-- TEMPLATE: Preview + Download --}}
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

                                                    {{-- PROPOSAL FILE: Preview + Download (FIX route param) --}}
                                                    @if($sourceLabel === 'ProposalFile' && $source)
                                                        @php
                                                            // pastikan relasi proposal ada (biar bisa route param lengkap)
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

                                                    {{-- DOCUMENT: Preview + Download (KONSISTEN) --}}
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
                                                    {{-- Setting --}}
                                                    <button type="button"
                                                            class="px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm"
                                                            data-modal-open="settingItem{{ $item->id }}">
                                                        Setting
                                                    </button>

                                                    {{-- Remove --}}
                                                    <form method="POST"
                                                          action="{{ route('folders.items.destroy', $item) }}"
                                                          class="delete-item-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>

                                                {{-- MODAL: SETTING --}}
                                                <div id="settingItem{{ $item->id }}"
                                                     class="hidden fixed inset-0 z-50 bg-black/50 items-center justify-center p-4">
                                                    <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg">
                                                        <div class="flex items-center justify-between px-5 py-4 border-b">
                                                            <h4 class="font-semibold text-gray-900">
                                                                Setting Dokumen
                                                            </h4>
                                                            <button type="button"
                                                                    class="text-gray-500 hover:text-gray-700"
                                                                    data-modal-close="settingItem{{ $item->id }}">
                                                                ✕
                                                            </button>
                                                        </div>

                                                        <div class="p-5 space-y-4">
                                                            <div class="text-sm text-gray-700">
                                                                <div class="font-medium">
                                                                    {{ $sourceLabel }} —
                                                                    @if($sourceLabel === 'Template')
                                                                        {{ $source?->title ?? '(Template hilang)' }}
                                                                    @elseif($sourceLabel === 'Document')
                                                                        {{ $source?->title ?? '(Dokumen hilang)' }}
                                                                    @else
                                                                        {{ $source?->original_name ?? '(File hilang)' }}
                                                                    @endif
                                                                </div>
                                                                <div class="text-xs text-gray-500 mt-1">
                                                                    Jika <b>Private</b> dicentang, dokumen tidak akan muncul di menu <b>Dokumen Dibagikan</b>.
                                                                </div>
                                                            </div>

                                                            <form method="POST"
                                                                  action="{{ route('folders.items.update', $item) }}"
                                                                  class="space-y-4">
                                                                @csrf
                                                                @method('PUT')

                                                                <label class="flex items-center gap-2 text-sm">
                                                                    <input type="hidden" name="is_private" value="0">
                                                                    <input type="checkbox"
                                                                           name="is_private"
                                                                           value="1"
                                                                           {{ $item->is_private ? 'checked' : '' }}>
                                                                    <span>Private (tidak dibagikan)</span>
                                                                </label>

                                                                {{-- Roles --}}
                                                                @php
                                                                    $roleOptions = array_keys($roleLabelMap);
                                                                    $selectedRoles = $item->shared_roles ?? [];
                                                                @endphp

                                                                <div>
                                                                    <div class="text-sm font-medium text-gray-800 mb-2">
                                                                        Share ke Roles
                                                                    </div>

                                                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                                                        @foreach($roleOptions as $r)
                                                                            <label class="flex items-center gap-2 text-sm">
                                                                                <input type="checkbox"
                                                                                       name="shared_roles[]"
                                                                                       value="{{ $r }}"
                                                                                       {{ in_array($r, $selectedRoles) ? 'checked' : '' }}>
                                                                                <span>{{ $roleLabelMap[$r] ?? $r }}</span>
                                                                            </label>
                                                                        @endforeach
                                                                    </div>
                                                                </div>

                                                                {{-- Bidang --}}
                                                                @php $selectedBidangs = $item->shared_bidang_ids ?? []; @endphp

                                                                <div>
                                                                    <div class="text-sm font-medium text-gray-800 mb-2">
                                                                        Share ke Bidang
                                                                    </div>

                                                                    <div class="grid grid-cols-2 gap-2">
                                                                        @foreach($bidangs as $b)
                                                                            <label class="flex items-center gap-2 text-sm">
                                                                                <input type="checkbox"
                                                                                       name="shared_bidang_ids[]"
                                                                                       value="{{ $b->id }}"
                                                                                       {{ in_array($b->id, $selectedBidangs) ? 'checked' : '' }}>
                                                                                <span>{{ $b->nama_bidang }}</span>
                                                                            </label>
                                                                        @endforeach
                                                                    </div>

                                                                    <div class="text-xs text-gray-500 mt-2">
                                                                        Tips: kalau mau share ke semua role tertentu, cukup centang roles saja tanpa bidang.
                                                                    </div>
                                                                </div>

                                                                <div class="flex justify-end gap-2 pt-2">
                                                                    <button type="button"
                                                                            class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm"
                                                                            data-modal-close="settingItem{{ $item->id }}">
                                                                        Tutup
                                                                    </button>

                                                                    <button type="submit"
                                                                            class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium">
                                                                        Simpan
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- END MODAL --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div id="noItemResult" class="hidden text-center py-10">
                            <div class="text-gray-900 font-semibold">Item tidak ditemukan</div>
                            <div class="text-sm text-gray-600 mt-1">Coba kata kunci lain.</div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @once
        <script>
            // Search items (client-side)
            const itemSearch = document.getElementById('itemSearch');
            const itemRows = document.querySelectorAll('.item-row');
            const noItemResult = document.getElementById('noItemResult');

            if (itemSearch) {
                itemSearch.addEventListener('input', () => {
                    const q = (itemSearch.value || '').toLowerCase().trim();
                    let visible = 0;

                    itemRows.forEach(row => {
                        const hay = row.getAttribute('data-search') || '';
                        const show = hay.includes(q);
                        row.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });

                    if (noItemResult) noItemResult.classList.toggle('hidden', visible !== 0);
                });
            }

            // Modal open/close
            document.querySelectorAll('[data-modal-open]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-modal-open');
                    const el = document.getElementById(id);
                    if (!el) return;
                    el.classList.remove('hidden');
                    el.classList.add('flex');
                });
            });

            document.querySelectorAll('[data-modal-close]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-modal-close');
                    const el = document.getElementById(id);
                    if (!el) return;
                    el.classList.add('hidden');
                    el.classList.remove('flex');
                });
            });

            // Klik backdrop untuk tutup modal
            document.querySelectorAll('[id^="settingItem"]').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }
                });
            });

            // SweetAlert2 confirm delete item
            document.querySelectorAll('.delete-item-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Hapus dokumen dari folder?',
                        text: 'Dokumen aslinya tidak terhapus, hanya dilepas dari folder.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e3342f',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        </script>
    @endonce
</x-app-layout>
