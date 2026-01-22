<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pengajuan Proposal
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">

                <form method="POST" action="{{ route('proposals.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="judul" value="Judul Proposal" />
                        <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full"
                                      value="{{ old('judul') }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('judul')" />
                    </div>

                    <div>
                        <x-input-label for="tujuan" value="Tujuan / Keterangan" />
                        <textarea id="tujuan" name="tujuan" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  required>{{ old('tujuan') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('tujuan')" />
                    </div>

                    <div>
                        <x-input-label for="files" value="Lampiran (PDF, bisa lebih dari 1 file)" />
                        <input id="files" name="files[]" type="file" multiple accept="application/pdf"
                               class="mt-1 block w-full text-sm text-gray-700
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-gray-100 file:text-gray-800
                                      hover:file:bg-gray-200"
                               required />
                        <x-input-error class="mt-2" :messages="$errors->get('files')" />
                        <x-input-error class="mt-2" :messages="$errors->get('files.*')" />
                        <p class="mt-2 text-xs text-gray-500">Maks 50MB per file.</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('proposals.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Kembali
                        </a>

                        <x-primary-button>
                            Submit Proposal
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
