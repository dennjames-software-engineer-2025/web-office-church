<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Notulensi</h2>
            <p class="text-sm text-gray-500">
                Informasi rapat dan catatan notulensi.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- TOP ACTIONS --}}
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <a href="{{ route('minutes.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    ← Kembali
                </a>

                <div class="flex items-center gap-2 flex-wrap">
                    @if(auth()->user()->canManageMinutes())
                        <a href="{{ route('minutes.edit', $minute) }}"
                           class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                            Edit
                        </a>

                        <form id="delete-minute-{{ $minute->id }}"
                              method="POST"
                              action="{{ route('minutes.destroy', $minute) }}"
                              class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>

                        <button type="button"
                                onclick="confirmDeleteMinute({{ $minute->id }})"
                                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
                            Hapus
                        </button>
                    @endif
                </div>
            </div>

            {{-- CONTENT CARD --}}
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-6">

                {{-- Title + Status --}}
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <div class="text-sm text-gray-500">Judul</div>
                        <div class="text-xl font-semibold text-gray-900">{{ $minute->title }}</div>
                    </div>

                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            {{ $minute->status === 'published' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $minute->status === 'published' ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                </div>

                <hr>

                {{-- Meta --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Tanggal & Jam Rapat</div>
                        <div class="text-gray-900 font-medium">
                            {{ optional($minute->meeting_at)->format('d-m-Y | H:i') ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Lokasi</div>
                        <div class="text-gray-900 font-medium">{{ $minute->location ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Pembuat</div>
                        <div class="text-gray-900 font-medium">{{ $minute->creator?->name ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Dibuat pada</div>
                        <div class="text-gray-900 font-medium">
                            {{ optional($minute->created_at)->format('d-m-Y | H:i') ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- Agenda --}}
                @if(!empty($minute->agenda))
                    <div>
                        <div class="text-sm text-gray-500">Agenda</div>
                        <div class="mt-1 text-gray-900 whitespace-pre-line">
                            {{ $minute->agenda }}
                        </div>
                    </div>
                @endif

                {{-- Isi Notulensi --}}
                <div>
                    <div class="text-sm text-gray-500">Isi Notulensi</div>

                    <div class="mt-2 rounded-lg border bg-gray-50 p-4">
                        <div class="text-gray-900 whitespace-pre-line leading-relaxed">
                            {{ $minute->content }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @once
        <script>
            function confirmDeleteMinute(id) {
                Swal.fire({
                    title: 'Hapus notulensi?',
                    text: "Notulensi yang dihapus tidak bisa dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-minute-' + id);
                        if (form) form.submit();
                    }
                });
            }
            window.confirmDeleteMinute = confirmDeleteMinute;
        </script>
    @endonce
</x-app-layout>
