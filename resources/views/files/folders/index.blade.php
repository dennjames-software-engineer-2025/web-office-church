<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Folder
            </h2>
            <p class="text-sm text-gray-500">
                Buat folder untuk menyimpan dokumen/template. Folder hanya terlihat oleh pembuatnya (kecuali Super Admin).
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if ($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">
                    <div class="font-semibold mb-2">Gagal menyimpan:</div>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- TOP BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="text-sm text-gray-600">
                        Total: <span class="font-semibold text-gray-900">{{ $folders->count() }}</span> folder
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        {{-- Search --}}
                        <div class="relative">
                            <input
                                id="folderSearch"
                                type="text"
                                placeholder="Cari nama folder..."
                                class="w-full sm:w-80 pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            >
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 21l-4.3-4.3"/>
                                    <circle cx="11" cy="11" r="7"/>
                                </svg>
                            </span>
                        </div>

                        {{-- Button scroll ke form --}}
                        {{-- <a href="#buat-folder"
                           class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
                            + Buat Folder
                        </a> --}}
                    </div>
                </div>
            </div>

            {{-- CARD: BUAT FOLDER --}}
            <div id="buat-folder" class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Buat Folder</h3>

                <form method="POST" action="{{ route('folders.store') }}"
                      class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    @csrf

                    <div class="sm:col-span-10">
                        <label class="block text-sm font-medium text-gray-700">Nama Folder</label>
                        <input
                            name="name"
                            value="{{ old('name') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            placeholder="Contoh: Dokumen Bidang Formatio"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">
                            Folder ini hanya terlihat oleh sekretaris yang membuatnya (kecuali Super Admin).
                        </p>
                    </div>

                    <div class="sm:col-span-2 flex sm:justify-end">
                        <button
                            type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

            {{-- CARD: DAFTAR FOLDER --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Folder</h3>
                        <div class="text-xs text-gray-500">Klik nama folder untuk melihat isi.</div>
                    </div>

                    @if($folders->isEmpty())
                        <div class="text-center py-10">
                            <div class="text-gray-900 font-semibold">Belum ada folder</div>
                            <div class="text-sm text-gray-600 mt-1">Buat folder terlebih dahulu untuk menyimpan dokumen.</div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                        <th class="px-4 py-3">Nama Folder</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody id="folderTableBody" class="divide-y">
                                    @foreach($folders as $f)
                                        <tr class="hover:bg-gray-50 folder-row"
                                            data-search="{{ strtolower($f->name) }}">
                                            <td class="px-4 py-3">
                                                <a class="text-blue-700 hover:underline font-medium"
                                                   href="{{ route('folders.show', $f) }}">
                                                    {{ $f->name }}
                                                </a>
                                            </td>

                                            <td class="px-4 py-3 text-center">
                                                <form method="POST"
                                                      action="{{ route('folders.destroy', $f) }}"
                                                      class="delete-folder-form inline"
                                                      data-folder-name="{{ $f->name }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- no-result --}}
                        <div id="noFolderResult" class="hidden text-center py-10">
                            <div class="text-gray-900 font-semibold">Folder tidak ditemukan</div>
                            <div class="text-sm text-gray-600 mt-1">Coba kata kunci lain.</div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @once
        <script>
            // Search folder (client-side)
            const folderSearch = document.getElementById('folderSearch');
            const folderRows = document.querySelectorAll('.folder-row');
            const noFolderResult = document.getElementById('noFolderResult');

            if (folderSearch) {
                folderSearch.addEventListener('input', () => {
                    const q = (folderSearch.value || '').toLowerCase().trim();
                    let visible = 0;

                    folderRows.forEach(row => {
                        const hay = row.getAttribute('data-search') || '';
                        const show = hay.includes(q);
                        row.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });

                    if (noFolderResult) noFolderResult.classList.toggle('hidden', visible !== 0);
                });
            }

            // SweetAlert2 Confirm Delete Folder
            document.querySelectorAll('.delete-folder-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const folderName = form.getAttribute('data-folder-name') || 'folder ini';

                    Swal.fire({
                        title: 'Hapus folder?',
                        text: `Folder "${folderName}" akan dihapus. Dokumen aslinya tidak ikut terhapus, tapi item dalam folder akan hilang dari folder ini.`,
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
