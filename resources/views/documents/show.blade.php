<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Dokumen
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Lihat informasi dokumen dan unduh file.
                </p>
            </div>

            <a href="{{ route('documents.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm">
                <span aria-hidden="true">←</span> Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- CARD UTAMA --}}
            <div class="bg-white shadow sm:rounded-lg p-6">
                {{-- HEADER DOKUMEN --}}
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div class="min-w-0">
                        <h3 class="text-2xl font-semibold text-gray-900 break-words">
                            {{ $document->title }}
                        </h3>

                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            {{-- Badge Bidang --}}
                            @php $bidang = optional($document->bidang)->nama_bidang; @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs
                                {{ $bidang ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                Bidang: {{ $bidang ?? 'Tim Inti' }}
                            </span>

                            {{-- Badge Sie --}}
                            @php $sie = optional($document->sie)->nama_sie; @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs
                                {{ $sie ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-700' }}">
                                Sie: {{ $sie ?? '-' }}
                            </span>

                            {{-- Badge Tanggal --}}
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700">
                                Upload: {{ $document->created_at?->format('d-m-Y | H:i') }}
                            </span>
                        </div>
                    </div>

                    {{-- AKSI --}}
                    <div class="flex flex-col sm:flex-row gap-2 shrink-0">
                        <a href="{{ route('documents.download', $document) }}"
                           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
                            <span aria-hidden="true">⬇</span> Unduh
                        </a>

                        @if(auth()->id() === $document->user_id || auth()->user()->hasRole('super_admin'))
                            <form id="delete-form-{{ $document->id }}"
                                  method="POST"
                                  action="{{ route('documents.destroy', $document) }}"
                                  class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>

                            <button type="button"
                                    onclick="confirmDeleteDocShow({{ $document->id }})"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium">
                                <span aria-hidden="true">🗑</span> Hapus
                            </button>
                        @endif
                    </div>
                </div>

                {{-- INFO GRID --}}
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-lg border p-4">
                        <div class="text-xs text-gray-500">Uploader</div>
                        <div class="mt-1 text-base font-semibold text-gray-900">
                            {{ $document->user?->name ?? '-' }}
                        </div>
                    </div>

                    <div class="rounded-lg border p-4">
                        <div class="text-xs text-gray-500">Nama File</div>
                        <div class="mt-1 text-base font-semibold text-gray-900 break-words">
                            {{ $document->filename }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            {{ $document->file_type ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- DESKRIPSI --}}
                <div class="mt-6">
                    <div class="text-sm font-semibold text-gray-900">Deskripsi</div>
                    <div class="mt-2 rounded-lg border bg-gray-50 p-4 text-gray-800 whitespace-pre-line">
                        {{ $document->description ?? '-' }}
                    </div>
                </div>

                {{-- SHARE (DOKUMEN INTI) --}}
                @if(is_null($document->bidang_id))
                    <div class="mt-6">
                        <div class="text-sm font-semibold text-gray-900">Dibagikan ke Bidang</div>

                        @if($document->sharedBidangs->isEmpty())
                            <div class="mt-2 text-sm text-gray-600">-</div>
                        @else
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($document->sharedBidangs as $b)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-blue-50 text-blue-700">
                                        {{ $b->nama_bidang }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- CATATAN KECIL --}}
            <div class="text-xs text-gray-500 px-1">
                Tips: Gunakan tombol “Unduh” untuk menyimpan file ke perangkat.
            </div>

        </div>
    </div>

    @once
        <script>
            function confirmDeleteDocShow(id) {
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
            window.confirmDeleteDocShow = confirmDeleteDocShow;
        </script>
    @endonce
</x-app-layout>
