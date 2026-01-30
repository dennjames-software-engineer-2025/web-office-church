{{-- resources/views/lpj/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Submit LPJ</h2>
            <p class="text-sm text-gray-500">Unggah dokumen LPJ untuk proposal yang sudah disetujui.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">

                {{-- HEADER CARD (konsisten seperti Proposal: tombol kembali di bawah header slot) --}}
                <div class="p-6 border-b bg-gray-50">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <a href="{{ route('proposals.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                            ← Kembali
                        </a>
                    </div>

                    <div class="mt-5">
                        <div class="text-xs text-gray-500">Proposal</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 break-words">{{ $proposal->judul }}</div>

                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-700">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs border bg-gray-50 text-gray-700 border-gray-200">
                                No Proposal: <span class="ml-1 font-semibold">{{ $proposal->proposal_no ?? '-' }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">

                    {{-- UPLOAD LPJ --}}
                    <div class="rounded-lg border bg-white p-6">
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div>
                                <div class="text-sm font-semibold text-gray-900">Upload Dokumen LPJ</div>
                                <p class="text-sm text-gray-600 mt-1">
                                    Isi Nama LPJ & Tanggal LPJ. Kamu boleh upload banyak file.
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('lpj.store', $proposal) }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-700">Nama LPJ</label>
                                    <input type="text" name="lpj_name" value="{{ old('lpj_name') }}"
                                           placeholder="Contoh: LPJ Kegiatan Natal 2026"
                                           class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                                    @error('lpj_name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div>
                                    <label class="text-sm text-gray-700">Tanggal LPJ</label>
                                    <input type="date" name="lpj_date" value="{{ old('lpj_date') }}"
                                           class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                                    @error('lpj_date') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="rounded-lg border border-dashed p-4 bg-gray-50">
                                <label class="text-sm font-medium text-gray-800">File LPJ</label>
                                <p class="text-xs text-gray-500 mt-1">PDF/JPG/PNG disarankan agar bisa preview. Maks 50MB/file.</p>

                                <input id="lpj-files" type="file" name="files[]" multiple
                                       class="mt-3 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400">

                                @error('files') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
                                @error('files.*') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror

                                <div id="file-preview" class="mt-4 hidden">
                                    <div class="text-sm font-semibold text-gray-900">Preview File Dipilih</div>
                                    <div id="file-preview-list" class="mt-2 space-y-3"></div>
                                    <div class="text-xs text-gray-500 mt-2">
                                        Catatan: Preview tersedia untuk PDF & gambar.
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
                                    <span aria-hidden="true">⬆</span> Kirim LPJ
                                </button>

                                <a href="{{ route('proposals.index') }}"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border bg-white hover:bg-gray-50 text-sm font-medium">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>

                    {{-- ✅ RIWAYAT LPJ sementara dimatikan karena sudah ada halaman index LPJ --}}
                    {{-- (Nanti bisa dihidupkan lagi kalau diminta) --}}

                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const input = document.getElementById('lpj-files');
            const box = document.getElementById('file-preview');
            const list = document.getElementById('file-preview-list');

            if (!input || !box || !list) return;

            input.addEventListener('change', () => {
                list.innerHTML = '';
                const files = Array.from(input.files || []);
                if (files.length === 0) {
                    box.classList.add('hidden');
                    return;
                }

                box.classList.remove('hidden');

                files.forEach((file, idx) => {
                    const row = document.createElement('div');
                    row.className = "rounded-lg border bg-white p-3";

                    const title = document.createElement('div');
                    title.className = "text-sm font-semibold text-gray-900";
                    title.textContent = `${idx + 1}. ${file.name}`;

                    const meta = document.createElement('div');
                    meta.className = "text-xs text-gray-500 mt-1";
                    meta.textContent = `${file.type || 'unknown'} • ${(file.size / 1024).toFixed(2)} KB`;

                    row.appendChild(title);
                    row.appendChild(meta);

                    const type = (file.type || '').toLowerCase();
                    if (type.includes('pdf')) {
                        const iframe = document.createElement('iframe');
                        iframe.className = "mt-3 w-full rounded-lg border";
                        iframe.style.height = "260px";
                        iframe.src = URL.createObjectURL(file);
                        row.appendChild(iframe);
                    } else if (type.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.className = "mt-3 w-full rounded-lg border object-contain";
                        img.style.maxHeight = "260px";
                        img.src = URL.createObjectURL(file);
                        row.appendChild(img);
                    }

                    list.appendChild(row);
                });
            });
        })();
    </script>
</x-app-layout>
