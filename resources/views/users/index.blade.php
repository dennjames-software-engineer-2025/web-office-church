<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen User
            </h2>
            <p class="text-sm text-gray-500">
                Kelola akun user berdasarkan kedudukan, jabatan, dan status.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- FILTER BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

                    {{-- Kedudukan --}}
                    <div class="md:col-span-3">
                        <x-input-label value="Kedudukan" />
                        <select name="kedudukan" class="mt-1 w-full rounded-md border-gray-300">
                            <option value="">Semua</option>
                            <option value="dpp_inti" {{ request('kedudukan')==='dpp_inti'?'selected':'' }}>DPP</option>
                            <option value="bgkp" {{ request('kedudukan')==='bgkp'?'selected':'' }}>BGKP</option>
                            <option value="sekretariat" {{ request('kedudukan')==='sekretariat'?'selected':'' }}>Sekretariat</option>
                            <option value="lingkungan" {{ request('kedudukan')==='lingkungan'?'selected':'' }}>Lingkungan</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="md:col-span-2">
                        <x-input-label value="Status" />
                        <select name="status" class="mt-1 w-full rounded-md border-gray-300">
                            <option value="">Semua</option>
                            <option value="active" {{ request('status')==='active'?'selected':'' }}>Active</option>
                            <option value="suspended" {{ request('status')==='suspended'?'selected':'' }}>Suspended</option>
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="md:col-span-4">
                        <x-input-label value="Cari" />
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Nama / Email / Jabatan"
                               class="mt-1 w-full rounded-md border-gray-300">
                    </div>

                    {{-- Action --}}
                    <div class="md:col-span-3 flex gap-2 justify-end">
                        <button class="px-4 py-2 bg-gray-100 rounded-md text-sm">Filter</button>
                        <a href="{{ route('users.index') }}" class="px-4 py-2 bg-white border rounded-md text-sm">Reset</a>

                        <a href="{{ route('users.create') }}"
                           class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">
                            + Buat User
                        </a>
                    </div>
                </form>
            </div>

            {{-- TABLE --}}
            <div class="bg-white shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Kedudukan</th>
                            <th class="px-4 py-3">Jabatan</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($users as $u)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-semibold">{{ $u->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $u->email }}</div>
                                </td>

                                <td class="px-4 py-3">
                                    {{ strtoupper(str_replace('_inti','',$u->kedudukan)) }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $u->jabatan_label }}
                                </td>

                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 text-xs rounded-full
                                        {{ $u->status==='active'
                                            ? 'bg-green-50 text-green-700'
                                            : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($u->status) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('users.show', $u) }}"
                                       class="px-3 py-1.5 bg-indigo-600 text-white rounded-md text-xs">
                                        Info
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-gray-500">
                                    Tidak ada data user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
