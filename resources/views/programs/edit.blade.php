<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Program
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">

                <form method="POST" action="{{ route('programs.update', $program) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label value="Nama Program" />
                        <x-text-input name="nama_program" class="mt-1 block w-full"
                                      value="{{ old('nama_program', $program->nama_program) }}" />
                    </div>

                    <div>
                        <x-input-label value="Deskripsi" />
                        <textarea name="deskripsi"
                                  class="mt-1 block w-full border-gray-300 rounded">{{ old('deskripsi', $program->deskripsi) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Tanggal Mulai" />
                            <x-text-input type="date" name="tanggal_mulai"
                                value="{{ old('tanggal_mulai', $program->tanggal_mulai) }}"
                                class="mt-1 block w-full" />
                        </div>

                        <div>
                            <x-input-label value="Tanggal Selesai" />
                            <x-text-input type="date" name="tanggal_selesai"
                                value="{{ old('tanggal_selesai', $program->tanggal_selesai) }}"
                                class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('programs.show', $program) }}"
                           class="px-4 py-2 bg-gray-200 rounded">
                            Batal
                        </a>

                        <x-primary-button>
                            Simpan Perubahan
                        </x-primary-button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
