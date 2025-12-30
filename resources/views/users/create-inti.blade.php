<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Akun Tim Inti
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Card --}}
            <div class="bg-white p-8 shadow rounded-lg">

                {{-- Subtitle --}}
                <p class="text-sm text-gray-600 mb-6">
                    Form ini hanya dapat digunakan oleh <strong>Super Admin</strong>.
                    Akun yang dibuat akan langsung berstatus <span class="font-semibold text-green-700">Active</span>.
                </p>

                {{-- GLOBAL ERROR --}}
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded">
                        <p class="font-semibold mb-2">Gagal menyimpan. Periksa kembali input berikut:</p>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- FORM --}}
                <form action="{{ route('users.store_inti') }}" method="POST">
                    @csrf

                    {{-- NAMA + EMAIL --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">

                        <div>
                            <x-input-label value="Nama Lengkap" />
                            <x-text-input
                                type="text"
                                name="name"
                                class="mt-1 block w-full"
                                placeholder="Contoh: Dennis Michael"
                                value="{{ old('name') }}"
                                required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="Email" />
                            <x-text-input
                                type="email"
                                name="email"
                                class="mt-1 block w-full"
                                placeholder="Contoh: user@gmail.com"
                                value="{{ old('email') }}"
                                required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                    </div>

                    {{-- JABATAN INTI --}}
                    <div class="mb-6">
                        <x-input-label value="Jabatan Tim Inti" />
                        <select name="jabatan_inti"
                                class="mt-1 block w-full border-gray-300 rounded"
                                required>
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach (['ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2'] as $j)
                                <option value="{{ $j }}" {{ old('jabatan_inti') === $j ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_',' ',$j)) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('jabatan_inti')" class="mt-2" />

                        <p class="text-sm text-gray-500 mt-1">
                            Jabatan menentukan hak akses dalam sistem.
                        </p>
                    </div>

                    {{-- PASSWORD --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">

                        <div>
                            <x-input-label value="Password" />
                            <x-text-input
                                name="password"
                                type="password"
                                class="mt-1 block w-full"
                                placeholder="Minimal 8 karakter"
                                required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="Konfirmasi Password" />
                            <x-text-input
                                name="password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                required />
                        </div>

                    </div>

                    {{-- BUTTONS --}}
                    <div class="flex justify-end gap-3 mt-8">

                        <a href="{{ route('users.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                            Batal
                        </a>

                        <x-primary-button>
                            Simpan Akun Tim Inti
                        </x-primary-button>

                    </div>

                </form>

            </div>
        </div>
    </div>

</x-app-layout>
