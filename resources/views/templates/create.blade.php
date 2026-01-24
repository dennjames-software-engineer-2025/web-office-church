<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Upload Template Baru
            </h2>
            <p class="text-sm text-gray-500">
                Upload file template (PDF/Word/Excel/PPT).
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div>
                <a href="{{ route('templates.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800">
                    ← Kembali
                </a>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6 space-y-6">

                    @if ($errors->any())
                        <div class="p-4 text-red-700 bg-red-50 border border-red-200 rounded-lg">
                            <div class="font-semibold">Gagal menyimpan template:</div>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('templates.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="title" value="Judul Template" />
                            <x-text-input id="title"
                                          name="title"
                                          type="text"
                                          class="mt-1 block w-full"
                                          value="{{ old('title') }}"
                                          required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="file" value="File Template" />
                            <input id="file"
                                   name="file"
                                   type="file"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                                   class="mt-1 block w-full rounded-lg border-gray-300"
                                   required />
                            <x-input-error class="mt-2" :messages="$errors->get('file')" />
                            <div class="text-xs text-gray-500 mt-2">Maks 10 MB.</div>
                        </div>

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('templates.index') }}"
                               class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800">
                                Batal
                            </a>

                            <button class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white font-medium">
                                Simpan
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
