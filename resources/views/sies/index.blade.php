<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Sie — {{ $bidang->nama_bidang }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- BUTTON KEMBALI --}}
            <div>
                <a href="{{ route('bidangs.index') }}"
                   class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    ← Kembali ke Bidang
                </a>
            </div>

            {{-- FORM TAMBAH SIE (permission) --}}
            @can('sie.manage')
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-3">Tambah Sie</h3>

                    <form action="{{ route('sies.store', $bidang) }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium">Nama Sie</label>
                            <input type="text" name="nama_sie"
                                   value="{{ old('nama_sie') }}"
                                   class="mt-1 w-full border-gray-300 rounded-lg"
                                   required>
                        </div>

                        <p class="text-sm text-gray-500">
                            Sie ini otomatis berada dalam bidang <strong>{{ $bidang->nama_bidang }}</strong>.
                        </p>

                        <div class="text-right">
                            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            @endcan

            {{-- TABLE SIE --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Daftar Sie</h3>

                <table class="min-w-full">
                    <thead class="bg-gray-100 text-sm uppercase text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-center">Nama Sie</th>
                            <th class="px-4 py-3 text-center">Jumlah Anggota</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse($sies as $sie)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-center">
                                    {{ $sie->nama_sie }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    {{ $sie->users->count() }} anggota
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if ($sie->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Aktif</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-300 text-gray-700 rounded text-xs">Nonaktif</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-center gap-2">
                                        {{-- DETAIL (boleh tampil untuk semua yang bisa akses halaman) --}}
                                        <button
                                            data-modal-target="detailSie{{ $sie->id }}"
                                            data-modal-toggle="detailSie{{ $sie->id }}"
                                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm">
                                            Detail
                                        </button>

                                        {{-- AKSI EDIT/HAPUS/TOGGLE (permission) --}}
                                        @can('sie.manage')
                                            <button
                                                data-modal-target="editSie{{ $sie->id }}"
                                                data-modal-toggle="editSie{{ $sie->id }}"
                                                class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded text-sm">
                                                Edit
                                            </button>

                                            <form action="{{ route('sies.destroy', $sie) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-3 py-1 bg-red-600 text-white rounded text-sm">
                                                    Hapus
                                                </button>
                                            </form>

                                            <form action="{{ route('sies.toggle', $sie) }}" method="POST">
                                                @csrf
                                                @method('PATCH')

                                                @if ($sie->is_active)
                                                    <button class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm">
                                                        Nonaktifkan
                                                    </button>
                                                @else
                                                    <button class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm">
                                                        Aktifkan
                                                    </button>
                                                @endif
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>

                            {{-- MODALS dipush ke bawah (jangan taruh di tbody) --}}
                            @push('modals')
                                {{-- MODAL DETAIL --}}
                                <div id="detailSie{{ $sie->id }}"
                                     class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                                    <div class="bg-white rounded-lg p-6 w-full max-w-xl">
                                        <h3 class="text-lg font-semibold mb-4">
                                            Detail Sie — {{ $sie->nama_sie }}
                                        </h3>

                                        <p><strong>Status:</strong> {{ $sie->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                                        <p><strong>Jumlah Anggota:</strong> {{ $sie->users->count() }}</p>

                                        <hr class="my-4">

                                        <h4 class="font-semibold mb-2">Daftar Anggota</h4>

                                        @forelse ($sie->users as $usr)
                                            <p class="text-sm">— {{ $usr->name }} ({{ $usr->email }})</p>
                                        @empty
                                            <p class="text-gray-500 text-sm">Belum ada anggota.</p>
                                        @endforelse

                                        <div class="text-right mt-6">
                                            <button data-modal-hide="detailSie{{ $sie->id }}"
                                                    class="px-4 py-2 bg-gray-200 rounded">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- MODAL EDIT (permission) --}}
                                @can('sie.manage')
                                    <div id="editSie{{ $sie->id }}"
                                         class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                                        <div class="bg-white rounded-lg p-6 w-full max-w-md">
                                            <h3 class="text-lg font-semibold mb-4">Edit Sie</h3>

                                            <form action="{{ route('sies.update', $sie) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <label class="block text-sm font-medium">Nama Sie</label>
                                                <input type="text" name="nama_sie"
                                                       value="{{ $sie->nama_sie }}"
                                                       class="mt-1 w-full border-gray-300 rounded"
                                                       required>

                                                <div class="mt-6 flex justify-end gap-2">
                                                    <button type="button"
                                                            data-modal-hide="editSie{{ $sie->id }}"
                                                            class="px-3 py-1 bg-gray-200 rounded">
                                                        Batal
                                                    </button>

                                                    <button class="px-3 py-1 bg-blue-600 text-white rounded">
                                                        Simpan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endcan
                            @endpush

                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-gray-600 py-4">
                                    Belum ada Sie untuk bidang ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- tempat render semua modal yang dipush --}}
    @stack('modals')

    {{-- SWEETALERT DELETE (hanya untuk user yang punya tombol delete) --}}
    @can('sie.manage')
        <script>
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e){
                    e.preventDefault();

                    Swal.fire({
                        title: 'Hapus Sie?',
                        text: 'Anggota tetap ada, tetapi tidak akan memiliki Sie.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Hapus'
                    }).then((result)=>{
                        if(result.isConfirmed){
                            form.submit();
                        }
                    });

                });
            });
        </script>
    @endcan

</x-app-layout>