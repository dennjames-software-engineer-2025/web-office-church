<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Pengesahan Dokumen
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        {{-- Alert --}}
        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('status') }}
            </div>
        @endif

        {{-- ===================================================== --}}
        {{-- BAGIAN ATAS — DOKUMEN PENDING --}}
        {{-- ===================================================== --}}
        <div class="bg-white shadow sm:rounded-lg p-6">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Permohonan Pengesahan</h3>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3">Nama Dokumen</th>
                            <th class="px-4 py-3">Pengirim</th>
                            <th class="px-4 py-3">Tujuan</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse ($pending as $d)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $d->original_name }}</td>
                                <td class="px-4 py-3">{{ $d->pengirim->name }}</td>
                                <td class="px-4 py-3">{{ $d->tujuan }}</td>
                                <td class="px-4 py-3">{{ $d->created_at->format('d M Y') }}</td>

                                <td class="px-4 py-3 text-center flex gap-2 justify-center">

                                    {{-- Lihat --}}
                                    <a href="{{ route('pengesahan.preview', $d) }}"
                                       target="_blank"
                                       class="px-3 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600">
                                        Lihat
                                    </a>

                                    {{-- Terima --}}
                                    <form action="{{ route('pengesahan.accept', $d) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">
                                            Terima
                                        </button>
                                    </form>

                                    {{-- Tolak --}}
                                    <a href="{{ route('pengesahan.rejectForm', $d) }}"
                                       class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                        Tolak
                                    </a>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">
                                    Tidak ada permohonan pengesahan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        {{-- ===================================================== --}}
        {{-- BAGIAN BAWAH — DOKUMEN ACCEPTED --}}
        {{-- ===================================================== --}}
        <div class="bg-white shadow sm:rounded-lg p-6">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Dokumen yang Telah Disahkan</h3>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3">Nama Dokumen</th>
                            <th class="px-4 py-3">Pengirim</th>
                            <th class="px-4 py-3">Disahkan Oleh</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse ($accepted as $d)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $d->original_name }}</td>
                                <td class="px-4 py-3">{{ $d->pengirim->name }}</td>
                                <td class="px-4 py-3">{{ $d->pengesah->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $d->tanggal_disahkan?->format('d M Y') }}</td>

                                <td class="px-4 py-3 text-center flex gap-2 justify-center">

                                    {{-- Surat --}}
                                    <a href="{{ route('pengesahan.suratForm', $d) }}"
                                       class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                                        Surat
                                    </a>

                                    {{-- Watermark --}}
                                    <a href="{{ route('pengesahan.watermarkForm', $d) }}"
                                       class="px-3 py-1 bg-purple-600 text-white rounded text-xs hover:bg-purple-700">
                                        Watermark
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('pengesahan.destroy', $d) }}" class="delete-form" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                            Hapus
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">
                                    Belum ada dokumen yang disahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

    </div>

    {{-- SWEETALERT DELETE --}}
    <script>
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus dokumen?',
                    text: 'Tindakan ini tidak bisa dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

</x-app-layout>
