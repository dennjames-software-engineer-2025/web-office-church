<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Notulensi</h2>
            <p class="text-sm text-gray-500">
                Perbarui informasi rapat dan isi notulensi. Pastikan jelas dan mudah dibaca.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('minutes.show', $minute) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    ← Kembali
                </a>

                <a href="{{ route('minutes.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border rounded-lg hover:bg-gray-50">
                    Lihat Daftar
                </a>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('minutes.update', $minute) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- Judul --}}
                    <div>
                        <x-input-label value="Judul Rapat" />
                        <x-text-input
                            name="title"
                            class="mt-1 block w-full"
                            required
                            value="{{ old('title', $minute->title) }}"
                        />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    {{-- Tanggal, Jam, Lokasi --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Tanggal & Jam Rapat" />
                            <input
                                type="datetime-local"
                                name="meeting_at"
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                required
                                value="{{ old('meeting_at', optional($minute->meeting_at)->format('Y-m-d\TH:i')) }}"
                            >
                            <x-input-error :messages="$errors->get('meeting_at')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="Lokasi (opsional)" />
                            <x-text-input
                                name="location"
                                class="mt-1 block w-full"
                                value="{{ old('location', $minute->location) }}"
                            />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Agenda --}}
                    <div>
                        <x-input-label value="Agenda (opsional)" />
                        <textarea
                            name="agenda"
                            rows="3"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            placeholder="Contoh: Evaluasi kegiatan, rencana jadwal, pembagian tugas..."
                        >{{ old('agenda', $minute->agenda) }}</textarea>
                        <x-input-error :messages="$errors->get('agenda')" class="mt-2" />
                    </div>

                    {{-- Isi --}}
                    <div>
                        <x-input-label value="Isi Notulensi" />
                        <textarea
                            name="content"
                            rows="10"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            placeholder="Tulis poin-poin rapat secara ringkas dan jelas..."
                            required
                        >{{ old('content', $minute->content) }}</textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>

                    {{-- Status --}}
                    <div>
                        <x-input-label value="Status" />
                        <select
                            name="status"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            required
                        >
                            <option value="draft" {{ old('status', $minute->status) === 'draft' ? 'selected' : '' }}>
                                Draft
                            </option>
                            <option value="published" {{ old('status', $minute->status) === 'published' ? 'selected' : '' }}>
                                Publish
                            </option>
                        </select>

                        <div class="text-xs text-gray-500 mt-1">
                            Draft = disiapkan dulu. Publish = siap dibaca semua user.
                        </div>

                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <a href="{{ route('minutes.show', $minute) }}"
                           class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Catatan kecil (ramah lansia) --}}
            <div class="text-xs text-gray-500">
                Tips: gunakan poin-poin singkat, dan pisahkan per topik agar mudah dibaca.
            </div>

        </div>
    </div>
</x-app-layout>