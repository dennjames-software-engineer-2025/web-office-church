<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail User
            </h2>

            <div class="flex items-center gap-2">
                <a href="{{ route('users.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                    Kembali
                </a>

                @can('users.manage')
                    <a href="{{ route('users.edit', $user) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Edit
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash error --}}
            @if (session('error'))
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Card: Info --}}
            <div class="bg-white p-6 sm:p-8 shadow sm:rounded-lg">
                <div class="flex items-start justify-between gap-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $user->email }}</p>
                    </div>

                    {{-- Status badge --}}
                    @php
                        $statusClass = match($user->status) {
                            'active'    => 'bg-green-50 text-green-700 border-green-200',
                            'pending'   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'rejected'  => 'bg-red-50 text-red-700 border-red-200',
                            'suspended' => 'bg-gray-100 text-gray-700 border-gray-200',
                            default     => 'bg-gray-50 text-gray-700 border-gray-200',
                        };

                        $kedudukanLabel = match($user->kedudukan) {
                            'dpp_inti'     => 'DPP Inti',
                            'bgkp'         => 'BGKP',
                            'lingkungan'   => 'Lingkungan',
                            'sekretariat'  => 'Sekretariat',
                            default        => '-',
                        };
                    @endphp

                    <div class="text-right space-y-2">
                        <span class="inline-flex items-center px-3 py-1 text-xs rounded-full font-semibold border {{ $statusClass }}">
                            {{ ucfirst($user->status) }}
                        </span>

                        <div class="text-xs text-gray-500">
                            Kedudukan: <span class="font-semibold text-gray-700">{{ $kedudukanLabel }}</span>
                        </div>
                    </div>
                </div>

                <div class="border-t my-6"></div>

                {{-- Detail --}}
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Jabatan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->jabatan_label }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Role (Spatie)</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @php
                                $roles = $user->roles->pluck('name')
                                    ->map(fn($r) => str_replace('_',' ', $r))
                                    ->map('ucwords');
                            @endphp
                            {{ $roles->isEmpty() ? '-' : $roles->join(', ') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Bidang</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $user->bidang?->nama_bidang ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sie</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $user->sie?->nama_sie ?? '-' }}
                        </dd>
                    </div>

                    {{-- Jika ditolak --}}
                    @if ($user->status === 'rejected' && $user->alasan_ditolak)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-red-600">Alasan Ditolak</dt>
                            <dd class="mt-2 text-sm text-gray-900 whitespace-pre-line bg-red-50 border border-red-200 rounded-md p-4">
                                {{ $user->alasan_ditolak }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Card: Danger Zone --}}
            @can('users.manage')
                <div class="bg-white p-6 sm:p-8 shadow sm:rounded-lg border border-red-100">
                    <h4 class="text-lg font-semibold text-gray-900">Danger Zone</h4>
                    <p class="text-sm text-gray-600 mt-1">
                        Menghapus akun akan melakukan <span class="font-semibold">soft delete</span>. Anda wajib mengisi alasan penghapusan.
                    </p>

                    <form id="deleteForm" method="POST" action="{{ route('users.destroy', $user) }}" class="mt-6">
                        @csrf
                        @method('DELETE')

                        <div>
                            <x-input-label for="alasan_dihapus" value="Alasan Penghapusan" />
                            <textarea
                                name="alasan_dihapus"
                                id="alasan_dihapus"
                                rows="4"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Contoh: Akun tidak digunakan lagi / terjadi pergantian personel / dll"
                                required>{{ old('alasan_dihapus') }}</textarea>
                            <x-input-error :messages="$errors->get('alasan_dihapus')" class="mt-2" />
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="button"
                                    onclick="confirmDelete()"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                Hapus Akun
                            </button>
                        </div>
                    </form>
                </div>
            @endcan

        </div>
    </div>

    <script>
        function confirmDelete() {
            const alasan = document.getElementById('alasan_dihapus').value.trim();

            if (!alasan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Alasan wajib diisi',
                    text: 'Anda harus memasukkan alasan penghapusan akun.'
                });
                return;
            }

            Swal.fire({
                title: "Yakin ingin menghapus akun ini?",
                text: "Tindakan tidak dapat dibatalkan.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#6b7280",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        }
    </script>
</x-app-layout>