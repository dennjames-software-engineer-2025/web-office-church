<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Template
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow sm:rounded-lg p-8 space-y-6">

                {{-- Judul --}}
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $template->title }}</h3>
                    <p class="text-gray-600">{{ $template->original_name }}</p>
                </div>

                <hr>

                {{-- Info --}}
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    <div>
                        <dt class="text-sm text-gray-500">Uploader</dt>
                        <dd class="text-sm text-gray-900">
                            {{ optional($template->uploader)->name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Ukuran File</dt>
                        <dd class="text-sm text-gray-900">
                            {{ number_format($template->size / 1024, 2) }} KB
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Tanggal Upload</dt>
                        <dd class="text-sm text-gray-900">
                            {{ $template->created_at->format('d-m-Y | H:i') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Tipe File</dt>
                        <dd class="text-sm text-gray-900">
                            {{ strtoupper($template->file_type) }}
                        </dd>
                    </div>

                    {{-- Shared --}}
                    <div class="sm:col-span-2">
                        <dt class="text-sm text-gray-500">Shared ke Bidang</dt>
                        <dd class="text-sm text-gray-900 mt-1">

                            @if($template->bidang_id)
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">
                                    {{ optional($template->bidang)->nama_bidang }}
                                </span>
                            @else
                                @if($template->bidangs->count() > 0)
                                    @foreach($template->bidangs as $b)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs mr-1">
                                            {{ $b->nama_bidang }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs">
                                        Private (Tim Inti & Super Admin)
                                    </span>
                                @endif
                            @endif
                        </dd>
                    </div>

                </dl>

                <hr>

                {{-- Aksi --}}
                <div class="flex justify-between items-center">

                    <div class="flex gap-3">

                        {{-- Preview --}}
                        @if(strtolower($template->file_type) === 'pdf')
                            <a href="{{ route('templates.stream', $template) }}" target="_blank"
                               class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                Preview
                            </a>
                        @else
                            <a href="{{ route('templates.view', $template) }}" target="_blank"
                               class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                Preview
                            </a>
                        @endif

                        {{-- Download --}}
                        <a href="{{ route('templates.download', $template) }}"
                           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Download
                        </a>

                    </div>

                    {{-- Delete --}}
                    @if(auth()->user()->can('delete', $template) || auth()->user()->hasRole('super_admin'))
                        <button onclick="deleteTemplate({{ $template->id }})"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Hapus Template
                        </button>
                    @endif

                </div>

            </div>

        </div>
    </div>


    {{-- SweetAlert Delete --}}
    <script>
        function deleteTemplate(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Tindakan ini tidak bisa dibatalkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Hapus'
            }).then(result => {
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
