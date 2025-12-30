<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail User
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                {{-- Header nama --}}
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h3>
                </div>

                <div class="border-t my-6"></div>

                {{-- Detail data --}}
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tim</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $user->team_type === 'inti' ? 'Tim Inti' : ($user->team_type === 'bidang' ? 'Tim Bidang' : '-') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Jabatan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->jabatan_label }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($user->status) }}</dd>
                    </div>

                    {{-- Jika ditolak --}}
                    @if ($user->status === 'rejected' && $user->alasan_ditolak)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-red-600">Alasan Ditolak</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">
                                {{ $user->alasan_ditolak }}
                            </dd>
                        </div>
                    @endif

                </dl>

                <div class="border-t my-6"></div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-between items-start">

                    {{-- Tombol kiri --}}
                    <div class="flex gap-3">
                        <a href="{{ route('users.index') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                            Kembali
                        </a>

                        <a href="{{ route('users.edit', $user) }}"
                           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Edit
                        </a>
                    </div>

                    {{-- Form hapus --}}
                    <form id="deleteForm" method="POST" action="{{ route('users.destroy', $user) }}">
                        @csrf
                        @method('DELETE')

                        <textarea name="alasan_dihapus" id="alasan_dihapus"
                                class="w-full border-gray-300 rounded" required></textarea>

                        <button type="button"
                                onclick="confirmDelete()"
                                class="mt-3 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 w-full">
                            Hapus Akun
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
    function confirmDelete() {
        let alasan = document.getElementById('alasan_dihapus').value.trim();

        if (alasan === "") {
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
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
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
