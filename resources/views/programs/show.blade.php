<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Program
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow sm:rounded-lg p-8 space-y-6">

                {{-- Judul --}}
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">
                        {{ $program->nama_program }}
                    </h3>
                </div>

                <hr>

                {{-- Status Badge --}}
                @php
                    $statusClass = match ($program->status) {
                        'draft'     => 'bg-gray-200 text-gray-800',
                        'berjalan'  => 'bg-blue-100 text-blue-800',
                        'selesai'   => 'bg-green-100 text-green-800',
                        default     => 'bg-gray-100 text-gray-700',
                    };
                @endphp

                {{-- Informasi --}}
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    <div class="sm:col-span-2">
                        <dt class="text-sm text-gray-500">Deskripsi Program</dt>
                        <dd class="text-sm text-gray-900 mt-1">
                            {{ $program->deskripsi ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($program->status) }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Tanggal Mulai</dt>
                        <dd class="text-sm text-gray-900">
                            {{ $program->tanggal_mulai }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Tanggal Selesai</dt>
                        <dd class="text-sm text-gray-900">
                            {{ $program->tanggal_selesai }}
                        </dd>
                    </div>

                </dl>

                <hr>

                {{-- Aksi --}}
                <div class="flex justify-between items-center">

                    {{-- Kiri --}}
                    <a href="{{ route('programs.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                        Kembali
                    </a>

                    {{-- Kanan --}}
                    <div class="flex gap-2">

                        @can('update', $program)
                            <a href="{{ route('programs.edit', $program) }}"
                               class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                Edit
                            </a>
                        @endcan

                        @can('delete', $program)
                            <button onclick="confirmDelete({{ $program->id }})"
                                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                Hapus
                            </button>
                        @endcan

                        @can('changeStatus', $program)
                            <form method="POST" action="{{ route('programs.change-status', $program) }}">
                                @csrf
                                @method('PATCH')

                                <select name="status"
                                        onchange="this.form.submit()"
                                        class="border-gray-300 rounded px-3 py-2 text-sm">

                                    <option value="draft" @selected($program->status === 'draft')>
                                        Draft
                                    </option>

                                    <option value="berjalan" @selected($program->status === 'berjalan')>
                                        Berjalan
                                    </option>

                                    <option value="selesai" @selected($program->status === 'selesai')>
                                        Selesai
                                    </option>
                                </select>
                            </form>
                        @endcan
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
