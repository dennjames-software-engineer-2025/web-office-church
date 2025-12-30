<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Dokumen') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Success --}}
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 shadow">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Upload Button --}}
            <div class="mb-4">
                <a href="{{ route('documents.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    + Upload Dokumen
                </a>
            </div>

            {{-- Document Table --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">

                    @if ($documents->isEmpty())
                        <p class="text-gray-600 text-sm">Belum ada dokumen.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-700 text-sm uppercase">
                                        <th class="px-4 py-3">Judul</th>
                                        <th class="px-4 py-3">Uploader</th>
                                        <th class="px-4 py-3">Bidang</th>
                                        <th class="px-4 py-3">Sie</th>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y">
                                    @foreach ($documents as $doc)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">{{ $doc->title }}</td>
                                            <td class="px-4 py-3">{{ $doc->user->name }}</td>
                                            <td class="px-4 py-3">{{ optional($doc->bidang)->nama_bidang ?? '-' }}</td>
                                            <td class="px-4 py-3">{{ optional($doc->sie)->nama_sie ?? '-' }}</td>
                                            <td class="px-4 py-3">{{ $doc->created_at->format('d-m-Y | H:i') }}</td>

                                            <td class="px-4 py-3 text-center flex gap-2 justify-center">
                                                {{-- Download --}}
                                                <a href="{{ route('documents.download', $doc) }}"
                                                   class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                                    Unduh
                                                </a>

                                                {{-- Delete --}}
                                                @hasanyrole('super_admin|tim_bidang')
                                                    <button type="button"
                                                        onclick="confirmDelete({{ $doc->id }})"
                                                        class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                                        Hapus
                                                    </button>
                                                @endhasanyrole
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>

                            <script>
                            function confirmDelete(id) {
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
                                        // Buat form invisible untuk submit DELETE
                                        let form = document.createElement('form');
                                        form.method = 'POST';
                                        form.action = '/documents/' + id;

                                        let csrf = document.createElement('input');
                                        csrf.type = 'hidden';
                                        csrf.name = '_token';
                                        csrf.value = '{{ csrf_token() }}';

                                        let method = document.createElement('input');
                                        method.type = 'hidden';
                                        method.name = '_method';
                                        method.value = 'DELETE';

                                        form.appendChild(csrf);
                                        form.appendChild(method);
                                        document.body.appendChild(form);

                                        form.submit();
                                    }
                                });
                            }
                            </script>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
