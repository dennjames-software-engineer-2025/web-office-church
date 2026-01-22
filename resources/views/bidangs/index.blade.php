<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kelola Bidang
            </h2>
            <p class="text-sm text-gray-500">
                Kelola struktur tim bawahan berdasarkan kedudukan (DPP, BGKP, Lingkungan, Sekretariat).
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- FLASH --}}
            @if (session('status'))
                <div class="rounded-lg bg-green-100 text-green-800 px-4 py-3 shadow">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-lg bg-red-100 text-red-800 px-4 py-3 shadow">
                    {{ session('error') }}
                </div>
            @endif

            {{-- TOP BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="text-sm text-gray-600">
                            Total: <span class="font-semibold text-gray-900">{{ $bidangs->count() }}</span> bidang
                        </div>

                        {{-- Filter Kedudukan --}}
                        <form method="GET" action="{{ route('bidangs.index') }}" class="flex items-center gap-2">
                            <label class="text-sm text-gray-600">Kedudukan:</label>
                            <select name="kedudukan"
                                    class="border-gray-300 rounded-lg text-sm"
                                    onchange="this.form.submit()">
                                @foreach($kedudukanOptions as $val => $label)
                                    <option value="{{ $val }}" {{ $kedudukan === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        {{-- Live Search --}}
                        <div class="relative">
                            <input
                                id="bidangSearch"
                                type="text"
                                placeholder="Cari nama bidang..."
                                class="w-full sm:w-80 pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            >
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 21l-4.3-4.3"/>
                                    <circle cx="11" cy="11" r="7"/>
                                </svg>
                            </span>
                        </div>

                        @can('bidang.manage')
                            <button type="button"
                                    onclick="document.getElementById('createCard').scrollIntoView({behavior:'smooth'})"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium">
                                <span class="text-lg leading-none">＋</span>
                                Tambah Bidang
                            </button>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- CREATE --}}
            @can('bidang.manage')
                <div id="createCard" class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tambah Bidang</h3>

                    <form action="{{ route('bidangs.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kedudukan</label>
                            <select name="kedudukan"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                    required>
                                @foreach($kedudukanOptions as $val => $label)
                                    <option value="{{ $val }}" {{ $kedudukan === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Bidang akan dibuat khusus untuk kedudukan ini.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Bidang</label>
                            <input type="text" name="nama_bidang"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                   required>
                            <p class="text-xs text-gray-500 mt-1">
                                Nama bidang unik per kedudukan (boleh sama di kedudukan berbeda).
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            @endcan

            {{-- TABLE --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    @if ($bidangs->isEmpty())
                        <div class="text-center py-10">
                            <div class="text-gray-900 font-semibold">Belum ada bidang</div>
                            <div class="text-sm text-gray-600 mt-1">
                                Tambahkan bidang untuk <b>{{ $kedudukanOptions[$kedudukan] ?? $kedudukan }}</b>.
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                        <th class="px-4 py-3">Bidang</th>
                                        <th class="px-4 py-3">Jumlah Sie</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody id="bidangBody" class="divide-y">
                                    @foreach ($bidangs as $bidang)
                                        <tr class="hover:bg-gray-50 bidang-row"
                                            data-search="{{ strtolower($bidang->nama_bidang) }}">
                                            <td class="px-4 py-3 font-medium text-gray-900">
                                                {{ $bidang->nama_bidang }}
                                            </td>

                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                                    {{ $bidang->sies_count ?? 0 }} Sie
                                                </span>
                                            </td>

                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                                    {{ $bidang->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                                    {{ $bidang->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center gap-2 flex-wrap">

                                                    {{-- Kelola Sie hanya untuk DPP Inti --}}
                                                    @if($bidang->kedudukan === 'dpp_inti')
                                                        <a href="{{ route('sies.index', $bidang) }}"
                                                           class="px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-sm">
                                                            Kelola Sie
                                                        </a>
                                                    @endif

                                                    {{-- DETAIL --}}
                                                    <button data-modal-target="detailBidang{{ $bidang->id }}"
                                                            data-modal-toggle="detailBidang{{ $bidang->id }}"
                                                            class="px-3 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 text-sm">
                                                        Detail
                                                    </button>

                                                    @can('bidang.manage')
                                                        {{-- EDIT --}}
                                                        <button data-modal-target="editBidang{{ $bidang->id }}"
                                                                data-modal-toggle="editBidang{{ $bidang->id }}"
                                                                class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                            Edit
                                                        </button>

                                                        {{-- TOGGLE --}}
                                                        <form method="POST" action="{{ route('bidangs.toggle', $bidang) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button class="px-3 py-2 rounded-lg text-sm
                                                                {{ $bidang->is_active ? 'bg-yellow-50 text-yellow-800 hover:bg-yellow-100' : 'bg-green-50 text-green-800 hover:bg-green-100' }}">
                                                                {{ $bidang->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                            </button>
                                                        </form>

                                                        {{-- DELETE --}}
                                                        <form method="POST" action="{{ route('bidangs.destroy', $bidang) }}"
                                                              class="delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div id="noResult" class="hidden text-center py-10">
                            <div class="text-gray-900 font-semibold">Bidang tidak ditemukan</div>
                            <div class="text-sm text-gray-600 mt-1">Coba kata kunci lain.</div>
                        </div>

                        {{-- MODAL di luar table --}}
                        @foreach ($bidangs as $bidang)
                            <div id="detailBidang{{ $bidang->id }}"
                                class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 p-4">
                                <div class="bg-white rounded-lg p-6 w-full max-w-xl">
                                    <h3 class="text-lg font-semibold mb-4">
                                        Detail Bidang — {{ $bidang->nama_bidang }}
                                    </h3>

                                    <p><strong>Kedudukan:</strong> {{ $kedudukanOptions[$bidang->kedudukan] ?? $bidang->kedudukan }}</p>
                                    <p><strong>Status:</strong> {{ $bidang->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                                    <p><strong>Jumlah Sie:</strong> {{ $bidang->sies_count ?? 0 }}</p>

                                    <div class="text-right mt-6">
                                        <button data-modal-hide="detailBidang{{ $bidang->id }}"
                                                class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                                            Tutup
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @can('bidang.manage')
                                <div id="editBidang{{ $bidang->id }}"
                                    class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 p-4">
                                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                                        <h3 class="text-lg font-semibold mb-4">Edit Bidang</h3>

                                        <form method="POST" action="{{ route('bidangs.update', $bidang) }}">
                                            @csrf
                                            @method('PUT')

                                            <label class="block text-sm font-medium">Nama Bidang</label>
                                            <input type="text" name="nama_bidang"
                                                class="mt-1 w-full border-gray-300 rounded-lg"
                                                value="{{ $bidang->nama_bidang }}" required>

                                            <div class="mt-6 flex justify-end gap-2">
                                                <button type="button"
                                                        data-modal-hide="editBidang{{ $bidang->id }}"
                                                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                                                    Batal
                                                </button>
                                                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                                    Simpan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endcan
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>

    @once
        <script>
            
            /* LIVE SEARCH client-side */
            const searchInput = document.getElementById('bidangSearch');
            const rows = document.querySelectorAll('.bidang-row');
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
            /* LIVE SEARCH client-side */

            /* SweetAlert delete */
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e){
                    e.preventDefault();

                    Swal.fire({
                        title: 'Hapus Bidang?',
                        text: 'Jika bidang ini punya Sie, semua Sie ikut terhapus. User tidak terhapus (bidang_id/sie_id menjadi null).',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result)=>{
                        if(result.isConfirmed){
                            form.submit();
                        }
                    });
                });
            });
            /* SweetAlert delete */

        </script>
    @endonce
</x-app-layout>
