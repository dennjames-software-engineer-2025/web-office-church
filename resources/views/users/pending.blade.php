<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            User Pending Approval
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Status --}}
            @if (session('status'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif
            {{-- End Status --}}

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($pendingUsers->isEmpty())
                        <p>Tidak ada user pending.</p>
                    @else
                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left">Nama</th>
                                    <th class="px-4 py-2 text-left">Email</th>
                                    <th class="px-4 py-2 text-left">Jabatan</th>
                                    <th class="px-4 py-2 text-left">Bidang</th>
                                    <th class="px-4 py-2 text-left">Sie</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingUsers as $user)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $user->name }}</td>
                                        <td class="px-4 py-2">{{ $user->email }}</td>
                                        <td class="px-4 py-2">{{ $user->jabatan ? $user->jabatan : 'Tidak Ada' }}</td>
                                        <td class="px-4 py-2">{{ optional($user->bidang)->nama_bidang ?? 'Tidak Ada' }}</td>
                                        <td class="px-4 py-2">{{ optional($user->sie)->nama_sie ?? 'Tidak Ada' }}</td>
                                        <td class="px-4 py-2">{{ $user->status }}</td>
                                        <td class="px-4 py-2">
                                            <form action="{{ route('users.approve', $user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">
                                                    Approve
                                                </button>
                                            </form>

                                            <form action="{{ route('users.reject', $user) }}" method="POST" class="inline ml-2">
                                                @csrf
                                                <div class="mb-2">
                                                    <textarea name="alasan" rows="2" class="border rounded w-full text-sm" placeholder="Alasan Penolakan">
                                                        {{ old('alasan') }}
                                                    </textarea>
                                                </div>
                                                <button type="submit" class="btn btn-danger">
                                                    Reject
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
