<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen User') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash message --}}
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-100 text-green-700 px-4 py-3 shadow">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Filter box --}}
            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">

                <form method="GET" action="{{ route('users.index') }}"
                      class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                    {{-- Filter Tim --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700">Tim</label>
                        <select name="team_type"
                                class="mt-1 w-full border-gray-300 rounded-md">
                            <option value="">Semua</option>
                            <option value="inti" {{ request('team_type') === 'inti' ? 'selected' : '' }}>Tim Inti</option>
                            <option value="bidang" {{ request('team_type') === 'bidang' ? 'selected' : '' }}>Tim Bidang</option>
                        </select>
                    </div>

                    {{-- Filter Status --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700">Status</label>
                        <select name="status"
                                class="mt-1 w-full border-gray-300 rounded-md">
                            <option value="">Semua</option>
                            <option value="pending"  {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active"   {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="rejected" {{ request('status') === 'rejected'? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3">

                        {{-- Tombol Filter --}}
                        <button type="submit"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
                            Filter
                        </button>

                        {{-- Tombol Reset --}}
                        <a href="{{ route('users.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                            Reset
                        </a>

                    </div>

                    {{-- Button Buat User --}}
                    <div class="flex justify-start md:justify-end">
                        <a href="{{ route('users.create_inti') }}"
                           class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition whitespace-nowrap">
                            Buat Akun User
                        </a>
                    </div>

                </form>

            </div>

            {{-- Users Table --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">

                    {{-- Jika tidak ada data --}}
                    @if ($users->isEmpty())
                        <p class="text-center text-gray-500 py-10">
                            Tidak ada akun user ditemukan.
                        </p>
                    @else

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-700 text-sm uppercase">
                                        <th class="px-4 py-3">Nama</th>
                                        <th class="px-4 py-3">Jabatan</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y">

                                    @foreach($users as $user)
                                        <tr class="hover:bg-gray-50">

                                            {{-- Nama --}}
                                            <td class="px-4 py-3">{{ $user->name }}</td>

                                            {{-- Jabatan --}}
                                            <td class="px-4 py-3 capitalize">{{ $user->jabatan_label }}</td>

                                            {{-- Status Badge --}}
                                            <td class="px-4 py-3">
                                                @php
                                                    $color = match($user->status) {
                                                        'active'   => 'bg-green-100 text-green-700',
                                                        'pending'  => 'bg-yellow-100 text-yellow-700',
                                                        'rejected' => 'bg-red-100 text-red-700',
                                                        default    => 'bg-gray-100 text-gray-700'
                                                    };
                                                @endphp

                                                <span class="px-3 py-1 text-xs rounded-full font-medium {{ $color }}">
                                                    {{ ucfirst($user->status) }}
                                                </span>
                                            </td>

                                            {{-- Aksi --}}
                                            <td class="px-4 py-3 text-center">
                                                <a href="{{ route('users.show', $user) }}"
                                                   class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition">
                                                    Info
                                                </a>
                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>

                    @endif

                </div>
            </div>

        </div>
    </div>

</x-app-layout>
