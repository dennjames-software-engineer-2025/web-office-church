<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Bidang
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Form Tambah Bidang --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">Tambah Bidang</h3>

                <form action="{{ route('bidangs.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">Nama Bidang</label>
                        <input type="text" name="nama_bidang"
                               class="mt-1 w-full border-gray-300 rounded-lg"
                               required>
                    </div>

                    <div class="text-right">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

            {{-- List Bidang --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Daftar Bidang</h3>

                <table class="min-w-full">
                    <thead class="bg-gray-100 text-sm uppercase text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama Bidang</th>
                            <th class="px-4 py-3 text-left">Jumlah Sie</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        @foreach ($bidangs as $bidang)
                            <tr class="hover:bg-gray-50">

                                <td class="px-4 py-3 font-medium">{{ $bidang->nama_bidang }}</td>

                                <td class="px-4 py-3">{{ $bidang->sies->count() }} Sie</td>

                                <td class="px-4 py-3">
                                    @if ($bidang->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Aktif</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-300 text-gray-700 rounded text-xs">Nonaktif</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 flex justify-end gap-2">

                                    {{-- Kelola Sie --}}
                                    <a href="{{ route('sies.index', $bidang) }}"
                                       class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm">
                                        Kelola Sie
                                    </a>

                                    {{-- Detail --}}
                                    <button data-modal-target="detailBidang{{ $bidang->id }}"
                                            data-modal-toggle="detailBidang{{ $bidang->id }}"
                                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm">
                                        Detail
                                    </button>

                                    {{-- Edit --}}
                                    <button data-modal-target="editBidang{{ $bidang->id }}"
                                            data-modal-toggle="editBidang{{ $bidang->id }}"
                                            class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded text-sm">
                                        Edit
                                    </button>

                                    {{-- Hapus --}}
                                    <form method="POST" action="{{ route('bidangs.destroy', $bidang) }}" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1 bg-red-600 text-white rounded text-sm">
                                            Hapus
                                        </button>
                                    </form>

                                    {{-- Toggle Aktiv --}}
                                    <form method="POST" action="{{ route('bidangs.toggle', $bidang) }}">
                                        @csrf
                                        @method('PATCH')

                                        @if($bidang->is_active)
                                            <button class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm">
                                                Nonaktifkan
                                            </button>
                                        @else
                                            <button class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm">
                                                Aktifkan
                                            </button>
                                        @endif

                                    </form>

                                </td>

                            </tr>

                            {{-- Modal Detail --}}
                            <div id="detailBidang{{ $bidang->id }}"
                                 class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">

                                <div class="bg-white rounded-lg p-6 w-full max-w-xl">
                                    <h3 class="text-lg font-semibold mb-4">
                                        Detail Bidang — {{ $bidang->nama_bidang }}
                                    </h3>

                                    <p><strong>Status:</strong> {{ $bidang->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                                    <p><strong>Jumlah Sie:</strong> {{ $bidang->sies->count() }}</p>

                                    <hr class="my-4">

                                    <h4 class="font-semibold mb-2">Ketua Bidang</h4>

                                    @if ($bidang->ketua)
                                        <p>{{ $bidang->ketua->name }}</p>
                                    @else
                                        <p class="text-red-600 text-sm">Belum ada Ketua Bidang.</p>
                                    @endif

                                    <hr class="my-4">

                                    <h4 class="font-semibold mb-2">Daftar Sie dan Anggota</h4>

                                    @forelse ($bidang->sies as $sie)
                                        <div class="mb-3">
                                            <p class="font-medium">{{ $sie->nama_sie }}</p>

                                            @if ($sie->users->count() > 0)
                                                <ul class="list-disc ml-5 text-sm">
                                                    @foreach ($sie->users as $usr)
                                                        <li>{{ $usr->name }} ({{ $usr->email }})</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="text-gray-500 text-sm ml-5">Tidak ada anggota.</p>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-gray-500">Tidak ada Sie.</p>
                                    @endforelse

                                    <div class="text-right mt-6">
                                        <button data-modal-hide="detailBidang{{ $bidang->id }}"
                                                class="px-4 py-2 bg-gray-200 rounded">
                                            Tutup
                                        </button>
                                    </div>

                                </div>
                            </div>

                            {{-- Modal Edit --}}
                            <div id="editBidang{{ $bidang->id }}"
                                 class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">

                                <div class="bg-white rounded-lg p-6 w-full max-w-md">

                                    <h3 class="text-lg font-semibold mb-4">Edit Bidang</h3>

                                    <form method="POST" action="{{ route('bidangs.update', $bidang) }}">
                                        @csrf
                                        @method('PUT')

                                        <label class="block text-sm font-medium">Nama Bidang</label>
                                        <input type="text" name="nama_bidang"
                                               class="mt-1 w-full border-gray-300 rounded"
                                               value="{{ $bidang->nama_bidang }}">

                                        <div class="mt-6 flex justify-end gap-2">
                                            <button type="button"
                                                    data-modal-hide="editBidang{{ $bidang->id }}"
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

                        @endforeach

                    </tbody>
                </table>

            </div>

        </div>
    </div>

    {{-- SweetAlert Delete --}}
    <script>
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e){
                e.preventDefault();

                Swal.fire({
                    title: 'Hapus Bidang?',
                    text: 'Semua sie di bidang ini juga akan ikut terhapus.',
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
        })
    </script>

</x-app-layout>
