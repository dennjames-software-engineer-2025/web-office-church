<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Notulensi</h2>
            <p class="text-sm text-gray-500">Isi informasi rapat dan catatan notulensi dengan jelas.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div>
                <a href="{{ route('minutes.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    ← Kembali
                </a>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('minutes.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label value="Judul Rapat" />
                        <x-text-input name="title" class="mt-1 block w-full" required value="{{ old('title') }}" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Tanggal & Jam Rapat" />
                            <input type="datetime-local" name="meeting_at"
                                   class="mt-1 block w-full rounded-lg border-gray-300"
                                   required value="{{ old('meeting_at') }}">
                            <x-input-error :messages="$errors->get('meeting_at')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="Lokasi (opsional)" />
                            <x-text-input name="location" class="mt-1 block w-full" value="{{ old('location') }}" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label value="Agenda (opsional)" />
                        <textarea name="agenda" rows="3"
                                  class="mt-1 block w-full rounded-lg border-gray-300">{{ old('agenda') }}</textarea>
                        <x-input-error :messages="$errors->get('agenda')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Isi Notulensi" />
                        <textarea name="content" rows="10"
                                  class="mt-1 block w-full rounded-lg border-gray-300"
                                  placeholder="Tulis poin-poin rapat secara ringkas dan jelas..."
                                  required>{{ old('content') }}</textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Status" />
                        <select name="status" class="mt-1 block w-full rounded-lg border-gray-300" required>
                            <option value="draft" {{ old('status','draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Publish</option>
                        </select>
                        <div class="text-xs text-gray-500 mt-1">
                            Draft hanya untuk disiapkan. Publish berarti siap dibaca semua user.
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>Simpan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
