<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Template
            </h2>
            <p class="text-sm text-gray-500">
                Informasi lengkap template dan akses file (preview / unduh).
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div>
                <a href="{{ route('templates.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800">
                    ← Kembali
                </a>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6 space-y-6">
                {{-- TITLE --}}
                <div>
                    <div class="text-sm text-gray-500">Judul</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $template->title }}</div>
                    <div class="text-sm text-gray-600 mt-1">{{ $template->original_name }}</div>
                </div>

                {{-- INFO GRID --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Uploader</div>
                        <div class="text-gray-900">{{ optional($template->uploader)->name ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Tanggal Upload</div>
                        <div class="text-gray-900">{{ optional($template->created_at)->format('d-m-Y | H:i') }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Tipe File</div>
                        <div class="text-gray-900">{{ strtoupper($template->file_type ?? '-') }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Ukuran</div>
                        <div class="text-gray-900">
                            {{ $template->size ? number_format($template->size / 1024, 2) . ' KB' : '-' }}
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <div class="text-gray-500">Shared</div>
                        <div class="text-gray-900 mt-1">
                            @if($template->bidang_id)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-50 text-blue-700">
                                    Bidang: {{ optional($template->bidang)->nama_bidang ?? '-' }}
                                </span>
                            @else
                                @if($template->bidangs && $template->bidangs->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($template->bidangs as $b)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-50 text-green-700">
                                                {{ $b->nama_bidang }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                        Private (Tim Inti & Super Admin)
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between pt-2 border-t">
                    <div class="flex gap-2 flex-wrap pt-4">
                        {{-- Preview --}}
                        @if(strtolower($template->file_type) === 'pdf')
                            <a href="{{ route('templates.stream', $template) }}" target="_blank"
                               class="px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                Preview
                            </a>
                        @else
                            <a href="{{ route('templates.view', $template) }}" target="_blank"
                               class="px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                Preview
                            </a>
                        @endif

                        {{-- Download --}}
                        <a href="{{ route('templates.download', $template) }}"
                           class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                            Unduh
                        </a>
                    </div>

                    @php
                        $user = auth()->user();
                        $isInti = $user->hasAnyRole(['super_admin','ketua','wakil_ketua','sekretaris','bendahara']);
                        $isNonInti = $user->hasAnyRole(['ketua_bidang','ketua_sie','anggota_komunitas']);
                        $canDelete =
                            $user->hasRole('super_admin')
                            || ($template->bidang_id === null && $isInti)
                            || ($template->bidang_id !== null && $isNonInti && (int)$template->uploaded_by === (int)$user->id);
                    @endphp

                    @if($canDelete)
                        <div class="pt-4 sm:pt-0">
                            <form id="delete-template-show"
                                  method="POST"
                                  action="{{ route('templates.destroy', $template) }}"
                                  class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>

                            <button type="button"
                                    onclick="confirmDeleteTemplateShow()"
                                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">
                                Hapus Template
                            </button>
                        </div>
                    @endif
                </div>

                @if(!empty($fileMissing) && $fileMissing)
                    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        File fisik tidak ditemukan di storage. Preview/Download tidak bisa dilakukan.
                    </div>
                @endif
            </div>

        </div>
    </div>

    @once
        <script>
            function confirmDeleteTemplateShow() {
                Swal.fire({
                    title: 'Hapus Template?',
                    text: "Tindakan ini permanen dan tidak dapat dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-template-show');
                        if (form) form.submit();
                    }
                });
            }
            window.confirmDeleteTemplateShow = confirmDeleteTemplateShow;
        </script>
    @endonce
</x-app-layout>
