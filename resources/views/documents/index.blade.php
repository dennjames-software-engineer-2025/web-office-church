<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Dokumen') }}
            </h2>
            <p class="text-sm text-gray-500">
                Lihat, unduh, dan kelola dokumen. Gunakan pencarian untuk cepat menemukan dokumen.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- TOP BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="text-sm text-gray-600">
                            Total: <span class="font-semibold text-gray-900">{{ $documents->count() }}</span> dokumen
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        {{-- Search --}}
                        <div class="relative">
                            <input
                                id="docSearch"
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
                        <a href="{{ route('documents.create') }}"
                           class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium">
                            <span class="text-lg leading-none">＋</span>
                            Upload Dokumen
                        </a>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    @if ($documents->isEmpty())
                        <div class="text-center py-10">
                            <div class="text-gray-900 font-semibold">Belum ada dokumen</div>
                            <div class="text-sm text-gray-600 mt-1">Klik tombol “Upload Dokumen” untuk menambahkan.</div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                        <th class="px-4 py-3">Dokumen</th>
                                        <th class="px-4 py-3">Uploader</th>
                                        <th class="px-4 py-3">Bidang</th>
                                        <th class="px-4 py-3">Sie</th>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody id="docTableBody" class="divide-y">
                                    @foreach ($documents as $doc)
                                        <tr class="hover:bg-gray-50 doc-row"
                                            data-search="{{ strtolower($doc->title.' '.$doc->user->name.' '.(optional($doc->bidang)->nama_bidang ?? '-').' '.(optional($doc->sie)->nama_sie ?? '-')) }}">

                                            {{-- DOKUMEN --}}
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-gray-900">
                                                    {{ $doc->title }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $doc->filename }}
                                                </div>
                                                @if(!empty($doc->description))
                                                    <div class="text-xs text-gray-600 mt-1">
                                                        {{ \Illuminate\Support\Str::limit($doc->description, 70) }}
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- UPLOADER --}}
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">{{ $doc->user->name }}</div>
                                            </td>

                                            {{-- BIDANG --}}
                                            <td class="px-4 py-3">
                                                @php $bidang = optional($doc->bidang)->nama_bidang; @endphp
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                                    {{ $bidang ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                                    {{ $bidang ?? '-' }}
                                                </span>
                                            </td>

                                            {{-- SIE --}}
                                            <td class="px-4 py-3">
                                                @php $sie = optional($doc->sie)->nama_sie; @endphp
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                                    {{ $sie ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-700' }}">
                                                    {{ $sie ?? '-' }}
                                                </span>
                                            </td>

                                            {{-- TANGGAL --}}
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">{{ $doc->created_at->format('d-m-Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $doc->created_at->format('H:i') }}</div>
                                            </td>

                                            {{-- AKSI (KONSISTEN) --}}
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center gap-2 flex-wrap">

                                                    {{-- ✅ Preview --}}
                                                    <a href="{{ route('documents.preview', $doc) }}" target="_blank"
                                                       class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                        Preview
                                                    </a>

                                                    {{-- ✅ Download --}}
                                                    <a href="{{ route('documents.download', $doc) }}"
                                                       class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                        Download
                                                    </a>

                                                    {{-- ✅ Hapus --}}
                                                    @if(auth()->id() === $doc->user_id || auth()->user()->hasRole('super_admin'))
                                                        <form id="delete-form-{{ $doc->id }}"
                                                              method="POST"
                                                              action="{{ route('documents.destroy', $doc) }}"
                                                              class="hidden">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>

                                                        <button type="button"
                                                                onclick="confirmDeleteDoc({{ $doc->id }})"
                                                                class="px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
                                                            Hapus
                                                        </button>
                                                    @endif

                                                    {{-- ✅ Folder (tetap) --}}
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
                                                                        <input type="hidden" name="source" value="document">
                                                                        <input type="hidden" name="source_id" value="{{ $doc->id }}">

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

                        <div id="noResult" class="hidden text-center py-10">
                            <div class="text-gray-900 font-semibold">Dokumen tidak ditemukan</div>
                            <div class="text-sm text-gray-600 mt-1">Coba kata kunci lain.</div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @once
        <script>
            const searchInput = document.getElementById('docSearch');
            const rows = document.querySelectorAll('.doc-row');
            const noResult = document.getElementById('noResult');

            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    const q = (searchInput.value || '').toLowerCase().trim();
                    let visible = 0;

                    rows.forEach(row => {
                        const hay = row.getAttribute('data-search') || '';
                        const show = hay.includes(q);
                        row.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });

                    if (noResult) noResult.classList.toggle('hidden', visible !== 0);
                });
            }

            function submitToFolderDoc(form) {
                const folderId = form.querySelector('[name="folder_id"]').value;
                if (!folderId) {
                    Swal.fire('Pilih folder dulu', '', 'warning');
                    return false;
                }
                form.action = `/folders/${folderId}/items`;
                return true;
            }

            function confirmDeleteDoc(id) {
                Swal.fire({
                    title: 'Hapus Dokumen?',
                    text: "Dokumen yang dihapus tidak bisa dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-form-' + id);
                        if (form) form.submit();
                    }
                });
            }

            window.submitToFolderDoc = submitToFolderDoc;
            window.confirmDeleteDoc = confirmDeleteDoc;
        </script>
    @endonce
</x-app-layout>
