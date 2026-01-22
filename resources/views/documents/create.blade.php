<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Upload Dokumen</h2>
            <p class="text-sm text-gray-500 mt-1">
                Isi judul, pilih file, lalu (opsional) tulis deskripsi agar mudah dicari.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- NAV --}}
            <div>
                <a href="{{ route('documents.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border bg-white hover:bg-gray-50">
                    <span class="text-lg">←</span>
                    <span class="font-medium">Kembali</span>
                </a>
            </div>

            {{-- CARD --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">

                    {{-- ERROR GLOBAL --}}
                    @if ($errors->any())
                        <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                            <div class="font-semibold text-red-700">Gagal menyimpan dokumen</div>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- JUDUL --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Judul Dokumen <span class="text-red-600">*</span>
                            </label>
                            <input id="title"
                                   name="title"
                                   type="text"
                                   value="{{ old('title') }}"
                                   required
                                   autofocus
                                   placeholder="Contoh: Laporan Keuangan Januari 2026"
                                   class="mt-2 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">
                                Gunakan judul yang jelas agar mudah dicari oleh pengurus.
                            </p>
                        </div>

                        {{-- FILE --}}
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700">
                                File Dokumen <span class="text-red-600">*</span>
                            </label>

                            <input id="file"
                                   name="file"
                                   type="file"
                                   required
                                   class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                                          file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                          file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-800
                                          hover:file:bg-gray-200">

                            @error('file')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <div class="mt-2 text-xs text-gray-500 space-y-1">
                                <div>• Ukuran maksimal: <b>10 MB</b></div>
                                <div>• Format: PDF / DOC / DOCX / XLS / XLSX / PPT / PPTX</div>
                            </div>
                        </div>

                        {{-- DESKRIPSI --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Deskripsi (opsional)
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="5"
                                      placeholder="Contoh: Ringkasan isi dokumen / catatan penting..."
                                      class="mt-2 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">
                                Deskripsi membantu pengurus memahami isi tanpa harus membuka file.
                            </p>
                        </div>

                        {{-- SHARE BIDANG (KHUSUS INTI) --}}
                        @if($isInti)
                            <div class="rounded-lg border bg-gray-50 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="text-lg">📢</div>
                                    <div>
                                        <div class="font-semibold text-gray-900">Bagikan ke Bidang (opsional)</div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Jika tidak dicentang, dokumen ini hanya terlihat oleh <b>Tim Inti</b>.
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($bidangs as $b)
                                        <label class="flex items-center gap-3 rounded-lg bg-white border p-3 hover:bg-gray-50">
                                            <input type="checkbox"
                                                   name="share_bidangs[]"
                                                   value="{{ $b->id }}"
                                                   class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                   @checked(in_array($b->id, old('share_bidangs', [])))>
                                            <span class="text-sm font-medium text-gray-800">{{ $b->nama_bidang }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- ACTIONS --}}
                        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                            <a href="{{ route('documents.index') }}"
                               class="w-full sm:w-auto px-5 py-3 rounded-lg border bg-white hover:bg-gray-50 text-center font-medium">
                                Batal
                            </a>

                            <button type="submit"
                                    class="w-full sm:w-auto px-5 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-semibold">
                                Simpan Dokumen
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
