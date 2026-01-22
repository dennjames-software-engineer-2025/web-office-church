<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen User
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash message --}}
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Filter --}}
            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('users.index') }}"
                      class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

                    {{-- Filter Tim --}}
                    <div class="md:col-span-3">
                        <x-input-label value="Tim" />
                        <select name="team_type"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Semua</option>
                            <option value="inti" {{ request('team_type') === 'inti' ? 'selected' : '' }}>Tim Inti</option>
                            <option value="bidang" {{ request('team_type') === 'bidang' ? 'selected' : '' }}>Tim Bidang</option>
                        </select>
                    </div>

                    {{-- Filter Status --}}
                    <div class="md:col-span-3">
                        <x-input-label value="Status" />
                        <select name="status"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Semua</option>
                            <option value="pending"  {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active"   {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>

                    {{-- Buttons Filter/Reset --}}
                    <div class="md:col-span-4 flex gap-3">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                            Filter
                        </button>

                        <a href="{{ route('users.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            Reset
                        </a>
                    </div>

                    {{-- Button Buat User --}}
                    <div class="md:col-span-2 flex md:justify-end">
                        @can('users.manage')
                            <a href="{{ route('users.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Buat Akun User
                            </a>
                        @endcan
                    </div>

                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">

                    @if ($users->isEmpty())
                        <p class="text-center text-gray-500 py-10">
                            Tidak ada akun user ditemukan.
                        </p>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold">Nama</th>
                                        <th class="px-4 py-3 font-semibold">Jabatan</th>
                                        <th class="px-4 py-3 font-semibold">Status</th>
                                        <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($users as $user)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            </td>

                                            <td class="px-4 py-3 capitalize">
                                                {{ $user->jabatan_label }}
                                            </td>

                                            <td class="px-4 py-3">
                                                @php
                                                    $color = match($user->status) {
                                                        'active'    => 'bg-green-50 text-green-700 border-green-200',
                                                        'pending'   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                        'rejected'  => 'bg-red-50 text-red-700 border-red-200',
                                                        'suspended' => 'bg-gray-100 text-gray-700 border-gray-200',
                                                        default     => 'bg-gray-50 text-gray-700 border-gray-200'
                                                    };
                                                @endphp

                                                <span class="inline-flex items-center px-3 py-1 text-xs rounded-full font-semibold border {{ $color }}">
                                                    {{ ucfirst($user->status) }}
                                                </span>
                                            </td>

                                            <td class="px-4 py-3 text-center">
                                                <a href="{{ route('users.show', $user) }}"
                                                   class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-md text-xs font-semibold hover:bg-indigo-700">
                                                    Info
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
