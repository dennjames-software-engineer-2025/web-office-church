<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Pengumuman</h2>
            <p class="text-sm text-gray-500">Buat informasi rapat/kegiatan untuk internal.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <a href="{{ route('announcements.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                ← Kembali
            </a>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('announcements.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label value="Judul" />
                        <x-text-input name="title" class="mt-1 block w-full" required value="{{ old('title') }}" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Isi Pengumuman" />
                        <textarea name="body" rows="8"
                                  class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                  required
                                  placeholder="Tulis pengumuman dengan jelas (contoh: jadwal rapat, tempat, agenda singkat).">{{ old('body') }}</textarea>
                        <x-input-error :messages="$errors->get('body')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Mulai tampil (opsional)" />
                            <input type="datetime-local" name="starts_at"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                   value="{{ old('starts_at') }}">
                            <x-input-error :messages="$errors->get('starts_at')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="Berakhir (opsional)" />
                            <input type="datetime-local" name="ends_at"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                   value="{{ old('ends_at') }}">
                            <x-input-error :messages="$errors->get('ends_at')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Status" />
                            <select name="status"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                    required>
                                <option value="published" {{ old('status','published') === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ old('status','published') === 'draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-2 pt-6">
                            <input type="hidden" name="is_pinned" value="0">
                            <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }}>
                            <span class="text-sm text-gray-800">Pin pengumuman</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 border rounded-lg p-4">
                        <div class="font-semibold text-gray-900">Target (opsional)</div>
                        <div class="text-xs text-gray-500 mt-1">
                            Jika kosong, pengumuman akan tampil untuk semua user.
                        </div>

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm font-medium text-gray-800 mb-2">Target Kedudukan</div>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($kedudukanOptions as $val => $label)
                                        <label class="flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="target_kedudukan[]" value="{{ $val }}"
                                                {{ in_array($val, old('target_kedudukan', [])) ? 'checked' : '' }}>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-gray-800 mb-2">Target Roles</div>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($roleOptions as $r)
                                        <label class="flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="target_roles[]" value="{{ $r }}"
                                                {{ in_array($r, old('target_roles', [])) ? 'checked' : '' }}>
                                            <span>{{ $r }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="text-xs text-gray-500 mt-2">
                                    Tips: Kalau mau share ke semua orang di kedudukan tertentu, cukup centang Kedudukan saja.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <a href="{{ route('announcements.index') }}"
                           class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
