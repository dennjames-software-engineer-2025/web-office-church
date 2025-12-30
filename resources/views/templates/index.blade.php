<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Template
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Upload --}}
            <div>
                <a href="{{ route('templates.create') }}"
                   class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    + Upload Template
                </a>
            </div>


            {{-- ===================================================== --}}
            {{-- TEMPLATE TIM INTI --}}
            {{-- ===================================================== --}}
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Template Tim Inti</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left border-collapse">
                        <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
                        <tr>
                            <th class="px-4 py-3">Judul</th>
                            <th class="px-4 py-3">Nama File</th>
                            <th class="px-4 py-3">Uploader</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y">

                        @forelse($templatesInti as $t)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $t->title }}</td>
                                <td class="px-4 py-3">{{ $t->original_name }}</td>
                                <td class="px-4 py-3">{{ optional($t->uploader)->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ optional($t->created_at)->format('d-m-Y | H:i') }}</td>

                                <td class="px-4 py-3">
                                    <div class="flex gap-2 justify-center">

                                        {{-- Detail --}}
                                        <a href="{{ route('templates.show', $t) }}"
                                           class="px-3 py-1 bg-gray-700 text-white rounded text-sm hover:bg-gray-800">
                                            Detail
                                        </a>

                                        {{-- Preview otomatis PDF / Google Docs --}}
                                        @if(strtolower($t->file_type) === 'pdf')
                                            <a href="{{ route('templates.stream', $t) }}" target="_blank"
                                               class="px-3 py-1 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600">
                                                Preview
                                            </a>
                                        @else
                                            <a href="{{ route('templates.view', $t) }}" target="_blank"
                                               class="px-3 py-1 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600">
                                                Preview
                                            </a>
                                        @endif

                                        {{-- Download --}}
                                        <a href="{{ route('templates.download', $t) }}"
                                           class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                            Download
                                        </a>

                                        {{-- Delete --}}
                                        @hasanyrole('super_admin|tim_inti')
                                            <button type="button"
                                                    onclick="deleteTemplate({{ $t->id }})"
                                                    class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                                                Hapus
                                            </button>
                                        @endhasanyrole
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-gray-600 py-4">
                                    Belum ada template inti.
                                </td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table>
                </div>
            </div>



            {{-- ===================================================== --}}
            {{-- TEMPLATE BIDANG --}}
            {{-- ===================================================== --}}
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Template Bidang</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left border-collapse">
                        <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
                        <tr>
                            <th class="px-4 py-3">Judul</th>
                            <th class="px-4 py-3">Nama File</th>
                            <th class="px-4 py-3">Bidang</th>
                            <th class="px-4 py-3">Uploader</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y">

                        @forelse($templatesBidang as $t)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $t->title }}</td>
                                <td class="px-4 py-3">{{ $t->original_name }}</td>
                                <td class="px-4 py-3">{{ optional($t->bidang)->nama_bidang }}</td>
                                <td class="px-4 py-3">{{ optional($t->uploader)->name }}</td>

                                <td class="px-4 py-3">
                                    <div class="flex gap-2 justify-center">

                                        {{-- Detail --}}
                                        <a href="{{ route('templates.show', $t) }}"
                                           class="px-3 py-1 bg-gray-700 text-white rounded text-sm hover:bg-gray-800">
                                            Detail
                                        </a>

                                        {{-- Preview --}}
                                        @if(strtolower($t->file_type) === 'pdf')
                                            <a href="{{ route('templates.stream', $t) }}" target="_blank"
                                               class="px-3 py-1 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600">
                                                Preview
                                            </a>
                                        @else
                                            <a href="{{ route('templates.view', $t) }}" target="_blank"
                                               class="px-3 py-1 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600">
                                                Preview
                                            </a>
                                        @endif

                                        {{-- Download --}}
                                        <a href="{{ route('templates.download', $t) }}"
                                           class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                            Download
                                        </a>

                                        {{-- Delete --}}
                                        @if(auth()->user()->hasRole('super_admin') ||
                                            (auth()->user()->hasRole('tim_bidang') && $t->uploaded_by === auth()->id()))

                                            <button type="button"
                                                    onclick="deleteTemplate({{ $t->id }})"
                                                    class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                                                Hapus
                                            </button>

                                        @endif
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-gray-600 py-4">
                                    Belum ada template bidang.
                                </td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>


    {{-- SWEETALERT DELETE --}}
    <script>
        function deleteTemplate(id) {
            Swal.fire({
                title: 'Hapus Template?',
                text: "Tindakan ini permanen dan tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/templates/' + id;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

</x-app-layout>
