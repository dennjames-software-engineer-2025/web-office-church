<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Template
            </h2>
            <p class="text-sm text-gray-500">
                Lihat, unduh, dan kelola template. Gunakan pencarian untuk cepat menemukan template.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            {{-- TOP BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="text-sm text-gray-600">
                        Total: <span class="font-semibold text-gray-900">{{ $templates->total() }}</span> template
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        {{-- Search --}}
                        <div class="relative">
                            <input
                                id="tplSearch"
                                type="text"
                                placeholder="Cari judul / uploader / bidang..."
                                class="w-full sm:w-80 pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            >
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 21l-4.3-4.3"/>
                                    <circle cx="11" cy="11" r="7"/>
                                </svg>
                            </span>
                        </div>

                        {{-- Upload --}}
                        <a href="{{ route('templates.create') }}"
                           class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium">
                            <span class="text-lg leading-none">＋</span>
                            Upload Template
                        </a>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    @if ($templates->isEmpty())
                        <div class="text-center py-10">
                            <div class="text-gray-900 font-semibold">Belum ada template</div>
                            <div class="text-sm text-gray-600 mt-1">Klik tombol “Upload Template” untuk menambahkan.</div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                        <th class="px-4 py-3">Template</th>
                                        <th class="px-4 py-3">Uploader</th>
                                        <th class="px-4 py-3">Bidang</th>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody id="tplBody" class="divide-y">
                                    @foreach($templates as $t)
                                        @php
                                            $uploaderName = optional($t->uploader)->name ?? '-';
                                            $bidangName = optional($t->bidang)->nama_bidang ?? '-';

                                            $hay = strtolower(
                                                ($t->title ?? '').' '.
                                                ($t->original_name ?? '').' '.
                                                ($uploaderName).' '.
                                                ($bidangName)
                                            );

                                            $canDelete = auth()->user()->hasRole('super_admin') || auth()->user()->can('files.manage');
                                        @endphp

                                        <tr class="hover:bg-gray-50 tpl-row" data-search="{{ $hay }}">
                                            {{-- TEMPLATE --}}
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-gray-900">
                                                    {{ $t->title }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $t->original_name }}
                                                </div>
                                            </td>

                                            {{-- UPLOADER --}}
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">{{ $uploaderName }}</div>
                                            </td>

                                            {{-- BIDANG --}}
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                                    {{ $bidangName !== '-' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                                    {{ $bidangName }}
                                                </span>
                                            </td>

                                            {{-- TANGGAL --}}
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">{{ optional($t->created_at)->format('d-m-Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ optional($t->created_at)->format('H:i') }}</div>
                                            </td>

                                            {{-- AKSI --}}
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center gap-2 flex-wrap">

                                                    {{-- Preview --}}
                                                    @if(strtolower($t->file_type) === 'pdf')
                                                        <a href="{{ route('templates.stream', $t) }}" target="_blank"
                                                           class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                            Preview
                                                        </a>
                                                    @else
                                                        <a href="{{ route('templates.view', $t) }}" target="_blank"
                                                           class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                            Preview
                                                        </a>
                                                    @endif

                                                    {{-- Download --}}
                                                    <a href="{{ route('templates.download', $t) }}"
                                                       class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                        Download
                                                    </a>

                                                    {{-- Hapus --}}
                                                    @if($canDelete)
                                                        <form id="delete-template-{{ $t->id }}"
                                                              method="POST"
                                                              action="{{ route('templates.destroy', $t) }}"
                                                              class="hidden">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>

                                                        <button type="button"
                                                                onclick="confirmDeleteTemplate({{ $t->id }})"
                                                                class="px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
                                                            Hapus
                                                        </button>
                                                    @endif

                                                    {{-- ✅ Folder (pakai pola yang sama seperti Documents, tanpa tambah route) --}}
                                                    @can('files.manage')
                                                        @if($folders->isEmpty())
                                                            <a href="{{ route('folders.index') }}"
                                                               class="px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-sm">
                                                                Buat Folder dulu
                                                            </a>
                                                        @else
                                                            <details class="relative">
                                                                <summary class="list-none cursor-pointer px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm inline-flex items-center gap-2">
                                                                    + Folder
                                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                        <path d="M6 9l6 6 6-6"/>
                                                                    </svg>
                                                                </summary>

                                                                <div class="absolute right-0 mt-2 w-64 bg-white border rounded-lg shadow-lg p-3 z-20">
                                                                    <form method="POST" onsubmit="return submitToFolderDoc(this);">
                                                                        @csrf
                                                                        {{-- bedanya: source & source_id adalah TEMPLATE --}}
                                                                        <input type="hidden" name="source" value="template">
                                                                        <input type="hidden" name="source_id" value="{{ $t->id }}">

                                                                        <label class="text-xs text-gray-600">Pilih folder</label>
                                                                        <select name="folder_id" class="mt-1 w-full border-gray-300 rounded text-sm">
                                                                            <option value="">Pilih folder...</option>
                                                                            @foreach($folders as $f)
                                                                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                                                                            @endforeach
                                                                        </select>

                                                                        <button type="submit"
                                                                                class="mt-3 w-full px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
                                                                            Simpan ke Folder
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </details>
                                                        @endif
                                                    @endcan
                                                    {{-- End Files Manage --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div id="tplNoResult" class="hidden text-center py-10">
                            <div class="text-gray-900 font-semibold">Template tidak ditemukan</div>
                            <div class="text-sm text-gray-600 mt-1">Coba kata kunci lain.</div>
                        </div>

                        <div class="mt-4">
                            {{ $templates->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @once
        <script>
            const tplSearch = document.getElementById('tplSearch');
            const tplRows = document.querySelectorAll('.tpl-row');
            const tplNoResult = document.getElementById('tplNoResult');

            if (tplSearch) {
                tplSearch.addEventListener('input', () => {
                    const q = (tplSearch.value || '').toLowerCase().trim();
                    let visible = 0;

                    tplRows.forEach(row => {
                        const hay = row.getAttribute('data-search') || '';
                        const show = hay.includes(q);
                        row.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });

                    if (tplNoResult) tplNoResult.classList.toggle('hidden', visible !== 0);
                });
            }

            function submitToFolderDoc(form) {
                const folderId = form.querySelector('[name="folder_id"]').value;
                if (!folderId) {
                    Swal.fire('Pilih folder dulu', '', 'warning');
                    return false;
                }
                // sama persis seperti Documents
                form.action = `/folders/${folderId}/items`;
                return true;
            }

            function confirmDeleteTemplate(id) {
                Swal.fire({
                    title: 'Hapus Template?',
                    text: "Tindakan ini permanen dan tidak dapat dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-template-' + id);
                        if (form) form.submit();
                    }
                });
            }

            window.submitToFolderDoc = submitToFolderDoc;
            window.confirmDeleteTemplate = confirmDeleteTemplate;
        </script>
    @endonce
</x-app-layout>
