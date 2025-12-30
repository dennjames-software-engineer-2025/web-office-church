<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Upload Template Baru
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow sm:rounded-lg p-8">

                {{-- Error global --}}
                @if ($errors->any())
                    <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
                        <b>Gagal menyimpan template:</b>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('templates.store') }}"
                      enctype="multipart/form-data">
                    @csrf

                    {{-- Judul --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Judul Template</label>
                        <input type="text" name="title"
                               class="mt-1 block w-full border rounded p-2"
                               value="{{ old('title') }}" required>
                    </div>

                    {{-- File --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">File Template</label>
                        <input type="file" name="file"
                               class="mt-1 block w-full border rounded p-2"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                               required>
                        <p class="text-gray-500 text-sm mt-1">Max 10 MB</p>
                    </div>

                    {{-- Checkbox share bidang (khusus Super Admin & Tim Inti) --}}
                    @hasanyrole('super_admin|tim_inti')
                        <div class="mb-6">
                            <label class="font-semibold text-gray-800">
                                Share Template ke Bidang:
                            </label>

                            <div class="grid grid-cols-2 gap-2 mt-2">
                                @foreach($bidangs as $b)
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="share_bidangs[]"
                                               value="{{ $b->id }}">
                                        {{ $b->nama_bidang }}
                                    </label>
                                @endforeach
                            </div>

                            <p class="text-gray-500 text-sm mt-1">
                                Jika tidak memilih apapun → Template hanya untuk Tim Inti & Super Admin.
                            </p>
                        </div>
                    @endhasanyrole

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('templates.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                            Batal
                        </a>

                        <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Upload Template
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

</x-app-layout>
