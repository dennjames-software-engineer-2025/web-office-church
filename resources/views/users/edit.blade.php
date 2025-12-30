<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit User — Tim Inti
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-8">

                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    {{-- Nama --}}
                    <div class="mb-6">
                        <x-input-label value="Nama" />
                        <x-text-input name="name" type="text"
                                      class="mt-1 block w-full"
                                      value="{{ old('name', $user->name) }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Email --}}
                    <div class="mb-6">
                        <x-input-label value="Email" />
                        <x-text-input name="email" type="email"
                                      class="mt-1 block w-full"
                                      value="{{ old('email', $user->email) }}" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Status --}}
                    <div class="mb-6">
                        <x-input-label value="Status" />
                        <select name="status"
                                class="mt-1 block w-full border-gray-300 rounded">
                            @foreach(['pending','active','rejected','suspended'] as $status)
                                <option value="{{ $status }}"
                                    {{ old('status', $user->status) === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    {{-- Jabatan Inti --}}
                    <div class="mb-6">
                        <x-input-label value="Jabatan Tim Inti" />
                        <select name="jabatan_inti"
                                class="mt-1 block w-full border-gray-300 rounded">
                            <option value="">-- Pilih Jabatan --</option>

                            @foreach(['ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2'] as $j)
                                <option value="{{ $j }}"
                                    {{ old('jabatan_inti', $user->jabatan) === $j ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_',' ',$j)) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('jabatan_inti')" class="mt-2" />
                    </div>

                    {{-- Aksi --}}
                    <div class="flex justify-end gap-3 mt-10">

                        <a href="{{ route('users.show', $user) }}"
                           class="px-5 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                            Batal
                        </a>

                        <x-primary-button>
                            Simpan
                        </x-primary-button>

                    </div>

                </form>

            </div>
        </div>
    </div>

</x-app-layout>
