<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tolak Pengesahan Dokumen
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Informasi Dokumen --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                    Dokumen: {{ $dokumen->original_name }}
                </h3>

                <p class="text-sm text-gray-700 mb-1">
                    <strong>Pengirim:</strong> {{ $dokumen->pengirim->name }}
                </p>

                <p class="text-sm text-gray-700 mb-4">
                    <strong>Tujuan:</strong> {{ $dokumen->tujuan }}
                </p>

                {{-- Button Lihat Dokumen --}}
                <a href="{{ route('pengesahan.preview', $dokumen) }}" target="_blank"
                   class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm">
                    Lihat Dokumen
                </a>

            </div>

            {{-- Form Penolakan --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mt-6">

                <form method="POST" action="{{ route('pengesahan.reject', $dokumen) }}">
                    @csrf

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700">
                            Alasan Penolakan
                        </label>

                        <textarea name="alasan" rows="4"
                                  class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm"
                                  placeholder="Contoh: Dokumen tidak lengkap, mohon revisi..."
                                  required>{{ old('alasan') }}</textarea>

                        @error('alasan')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('pengesahan.index') }}"
                           class="px-4 py-2 bg-gray-200 rounded text-gray-800 hover:bg-gray-300">
                            Batal
                        </a>

                        <button type="submit"
                            class="px-5 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Kirim Penolakan
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </div>

</x-app-layout>
