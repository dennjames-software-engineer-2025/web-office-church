<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload Dokumen') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Tombol Kembali --}}
            <x-secondary-button href="{{ route('documents.index') }}" class="mb-3">
                Kembali
            </x-secondary-button>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">

                    {{-- Form Upload --}}
                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Judul --}}
                        <div>
                            <x-input-label for="title" value="Judul Dokumen" />
                            <x-text-input id="title" name="title" type="text"
                                class="mt-1 block w-full" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        {{-- File --}}
                        <div class="mt-4">
                            <x-input-label for="file" value="File" />
                            <input id="file" name="file" type="file"
                                   class="mt-1 block w-full border-gray-300 rounded" required>
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mt-4">
                            <x-input-label for="description" value="Deskripsi (opsional)" />
                            <textarea id="description" name="description" rows="4"
                                class="mt-1 block w-full border-gray-300 rounded">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="mt-6 flex justify-end">
                            <x-primary-button>
                                Simpan
                            </x-primary-button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>
