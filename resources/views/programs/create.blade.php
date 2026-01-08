<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Program
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Tombol Kembali --}}
            <x-secondary-button href="{{ route('programs.index') }}" class="mb-3">
                Kembali
            </x-secondary-button>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">

                    <form method="POST" action="{{ route('programs.store') }}">
                        @csrf

                        {{-- Nama Program --}}
                        <div>
                            <x-input-label for="nama_program" value="Nama Program" />
                            <x-text-input
                                id="nama_program"
                                name="nama_program"
                                type="text"
                                class="mt-1 block w-full"
                                value="{{ old('nama_program') }}"
                                required
                                autofocus
                            />
                            <x-input-error :messages="$errors->get('nama_program')" class="mt-2" />
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mt-4">
                            <x-input-label for="deskripsi" value="Deskripsi (opsional)" />
                            <textarea
                                id="deskripsi"
                                name="deskripsi"
                                rows="4"
                                class="mt-1 block w-full border-gray-300 rounded"
                            >{{ old('deskripsi') }}</textarea>
                            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                        </div>

                        {{-- Tanggal --}}
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="tanggal_mulai" value="Tanggal Mulai" />
                                <x-text-input
                                    id="tanggal_mulai"
                                    name="tanggal_mulai"
                                    type="date"
                                    class="mt-1 block w-full"
                                    value="{{ old('tanggal_mulai') }}"
                                    required
                                />
                                <x-input-error :messages="$errors->get('tanggal_mulai')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="tanggal_selesai" value="Tanggal Selesai" />
                                <x-text-input
                                    id="tanggal_selesai"
                                    name="tanggal_selesai"
                                    type="date"
                                    class="mt-1 block w-full"
                                    value="{{ old('tanggal_selesai') }}"
                                    required
                                />
                                <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Submit --}}
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
