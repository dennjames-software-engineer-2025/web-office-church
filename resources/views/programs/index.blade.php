<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Program
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Create Button --}}
            @can('program.create')
                <div>
                    <a href="{{ route('programs.create') }}"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        + Buat Program
                    </a>
                </div>
            @endcan

            {{-- Program List --}}
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Daftar Program
                </h3>

                @if ($programs->isEmpty())
                    <p class="text-gray-600 text-sm">
                        Belum ada program yang dibuat.
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left border-collapse">
                            <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
                                <tr>
                                    <th class="px-4 py-3">Nama Program</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                @foreach ($programs as $program)
                                    @php
                                        $statusClass = match ($program->status) {
                                            'draft'     => 'inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-500 text-white',
                                            'berjalan'  => 'inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-blue-600 text-white dark:bg-blue-500',
                                            'selesai'   => 'inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-yellow-500 text-white',
                                            default     => 'inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-500 text-white',
                                        };
                                    @endphp

                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium">
                                            {{ $program->nama_program }}
                                        </td>

                                        <td class="px-4 py-3 text-sm">
                                            {{ $program->tanggal_mulai }} –
                                            {{ $program->tanggal_selesai }}
                                        </td>

                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded text-xs {{ $statusClass }}">
                                                {{ ucfirst($program->status) }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-3">
                                            <div class="flex gap-2 justify-center">
                                                @can('program.view')
                                                    <a href="{{ route('programs.show', $program) }}"
                                                    class="px-3 py-1 bg-gray-700 text-white rounded text-sm hover:bg-gray-800">
                                                        Detail
                                                    </a>
                                                @endcan

                                                @can('program.delete_pending')
                                                    <button
                                                        onclick="confirmDelete({{ $program->id }})"
                                                        class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                                                        Hapus
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        const destroyUrlTemplate = "{{ route('programs.destroy', ':id') }}";

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Program?',
                text: 'Program yang dihapus tidak bisa dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = destroyUrlTemplate.replace(':id', id);

                    let csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';

                    let method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';

                    form.appendChild(csrf);
                    form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

</x-app-layout>
