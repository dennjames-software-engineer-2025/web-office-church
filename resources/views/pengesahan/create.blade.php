<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pengajuan Pengesahan Dokumen
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Message --}}
            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Error --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Card --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('pengesahan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Tujuan --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700">Tujuan Pengesahan</label>
                        <input
                            type="text"
                            name="tujuan"
                            placeholder="Contoh: Pengesahan surat kegiatan Natal 2025"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm"
                            required
                        >
                    </div>

                    {{-- Upload File --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700">Upload Dokumen (PDF saja)</label>
                        <input
                            type="file"
                            name="file"
                            accept="application/pdf"
                            class="mt-2 block w-full text-sm text-gray-700 
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-blue-600 file:text-white
                                   hover:file:bg-blue-700"
                            required
                        >
                        <p class="mt-2 text-xs text-gray-500">Max size: 10MB. Format: PDF.</p>
                    </div>

                    {{-- Submit --}}
                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700">
                            Kirim Pengajuan
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>