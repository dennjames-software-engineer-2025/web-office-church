<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Program
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-secondary-button href="{{ route('programs.index') }}" class="mb-3">
                Kembali
            </x-secondary-button>

            {{-- CARD 1: PROGRAM --}}
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
                        'draft'     => 'inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-500 text-white',
                        'berjalan'  => 'inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-blue-600 text-white',
                        'selesai'   => 'inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-yellow-500 text-white',
                        default     => 'inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-500 text-white',
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
                            <span class="{{ $statusClass }}">
                                {{ ucfirst($program->status) }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Target Kedudukan</dt>
                        <dd class="text-sm text-gray-900">
                            @php
                                $label = [
                                    'dpp_inti' => 'DPP Inti',
                                    'lingkungan' => 'Lingkungan',
                                    'bgkp' => 'BGKP',
                                    'sekretariat' => 'Sekretariat',
                                ][$program->target_kedudukan] ?? '-';
                            @endphp
                            {{ $label }}
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

                {{-- Aksi Program --}}
                <div class="flex justify-between items-center">

                    <div class="flex gap-2">
                        @can('update', $program)
                            <a href="{{ route('programs.edit', $program) }}"
                               class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                Edit
                            </a>
                        @endcan

                        @can('delete', $program)
                            <button
                                onclick="confirmDeleteProgram('{{ route('programs.destroy', $program) }}')"
                                class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
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
                                    <option value="draft" @selected($program->status === 'draft')>Draft</option>
                                    <option value="berjalan" @selected($program->status === 'berjalan')>Berjalan</option>
                                    <option value="selesai" @selected($program->status === 'selesai')>Selesai</option>
                                </select>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- CARD 2: PROPOSALS --}}
            <div class="bg-white shadow sm:rounded-lg p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Proposal</h3>

                    @can('proposal.upload')
                        <a href="{{ route('programs.proposals.create', $program) }}"
                           class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                            + Upload Proposal
                        </a>
                    @endcan
                </div>

                @php
                    $isBendahara = auth()->user()->hasRole('bendahara');

                    $visibleProposals = $isBendahara
                        ? $program->proposals->where('status', 'diterima')
                        : $program->proposals;
                @endphp

                @if ($visibleProposals->isEmpty())
                    <p class="text-sm text-gray-600">
                        {{ $isBendahara ? 'Belum ada proposal yang disetujui ketua.' : 'Belum ada proposal.' }}
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left border-collapse">
                            <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
                                <tr>
                                    <th class="px-4 py-3">Judul</th>
                                    <th class="px-4 py-3">Status</th>
                                    {{-- <th class="px-4 py-3">Stage</th> --}}
                                    @if (!$isBendahara)
                                        <th class="px-4 py-3">Stage</th>
                                    @endif
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                @foreach ($visibleProposals as $proposal)
                                    @php
                                        $statusBadge = match ($proposal->status) {
                                            'review' => 'bg-blue-100 text-blue-800',
                                            'diterima' => 'bg-green-100 text-green-800',
                                            'ditolak' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };

                                        $stageLabel = match ($proposal->stage) {
                                            'ketua' => 'Ketua',
                                            'bendahara_1' => 'Bendahara 1',
                                            'bendahara_2' => 'Bendahara 2',
                                            default => '-',
                                        };
                                    @endphp

                                    <tr class="hover:bg-gray-50 align-top">
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            {{ $proposal->judul }}
                                            <div class="text-xs text-gray-500">
                                                {{ optional($proposal->created_at)->format('d M Y H:i') ?? '-' }}
                                            </div>

                                            {{-- Lampiran Proposal --}}
                                            @if($proposal->files->count())
                                                <div class="mt-2 space-y-1 text-xs">
                                                    <div class="text-gray-500">Lampiran:</div>

                                                    @foreach($proposal->files as $file)
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-gray-700">{{ $file->original_name }}</span>

                                                            @can('viewFile', $proposal)
                                                                <a target="_blank"
                                                                href="{{ route('programs.proposals.files.preview', [$program, $proposal, $file]) }}"
                                                                class="px-2 py-1 rounded bg-gray-100 hover:bg-gray-200">
                                                                    Preview
                                                                </a>
                                                            @endcan

                                                            @can('downloadFile', $proposal)
                                                                <a href="{{ route('programs.proposals.files.download', [$program, $proposal, $file]) }}"
                                                                class="px-2 py-1 rounded bg-gray-100 hover:bg-gray-200">
                                                                    Download
                                                                </a>
                                                            @endcan
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusBadge }}">
                                                {{ ucfirst($proposal->status) }}
                                            </span>
                                        </td>

                                        @if (!$isBendahara)
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                {{ $stageLabel }}
                                            </td>
                                        @endif

                                        {{-- <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ $stageLabel }}
                                        </td> --}}

                                        <td class="px-4 py-3">
                                            {{-- bikin aksi jadi 2 baris supaya rapi --}}
                                            <div class="flex flex-col items-center gap-2">

                                                {{-- Row tombol --}}
                                                @if($proposal->status === 'review')
                                                    @can('approve', $proposal)
                                                        <form method="POST" action="{{ route('programs.proposals.approve', [$program, $proposal]) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                                                                Approve
                                                            </button>
                                                        </form>
                                                    @endcan

                                                    {{-- Reject inline (opsi A) --}}
                                                    @can('reject', $proposal)
                                                        <details class="w-full">
                                                            <summary class="text-xs text-red-700 cursor-pointer select-none text-center">
                                                                Reject (isi alasan)
                                                            </summary>

                                                            <form method="POST"
                                                                action="{{ route('programs.proposals.reject', [$program, $proposal]) }}"
                                                                class="mt-2 space-y-2">
                                                                @csrf
                                                                @method('PATCH')

                                                                <textarea name="reject_reason" rows="3"
                                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                                    placeholder="Tuliskan alasan penolakan..." required></textarea>

                                                                <button class="w-full px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                                                                    Submit Reject
                                                                </button>
                                                            </form>
                                                        </details>
                                                    @endcan
                                                @endif
                                                {{-- End --}}

                                                @if ($proposal->status === 'ditolak' && $proposal->reject_reason)
                                                    <div class="w-full text-xs text-red-700 bg-red-50 rounded p-2">
                                                        <span class="font-semibold">Alasan:</span> {{ $proposal->reject_reason }}
                                                    </div>
                                                @endif
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

    {{-- Confirm Delete Program (tanpa SweetAlert, aman copas) --}}
    <script>
        function confirmDeleteProgram(actionUrl) {
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
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = actionUrl;

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';

                    const method = document.createElement('input');
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
