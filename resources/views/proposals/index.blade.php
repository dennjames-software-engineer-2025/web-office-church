<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengajuan Proposal</h2>
            <p class="text-sm text-gray-500">
                Ajukan, review, dan proses persetujuan proposal sesuai alur (Sie → Ketua Bidang → DPP Harian → Romo → Bendahara).
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- TOP BAR --}}
            <div class="bg-white shadow sm:rounded-lg p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="text-sm text-gray-600">
                        Total: <span class="font-semibold text-gray-900">{{ $proposals->total() }}</span> proposal
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                        {{-- Live Search --}}
                        <div class="relative">
                            <input
                                id="proposalSearch"
                                type="text"
                                placeholder="Cari judul / bidang / sie / pengaju / status..."
                                class="w-full sm:w-96 pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                            >
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 21l-4.3-4.3"/>
                                    <circle cx="11" cy="11" r="7"/>
                                </svg>
                            </span>
                        </div>

                        {{-- Buat Proposal --}}
                        @can('create', \App\Models\Proposal::class)
                            <a href="{{ route('proposals.create') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium">
                                <span class="text-lg leading-none">＋</span>
                                Ajukan Proposal
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    @if ($proposals->isEmpty())
                        <div class="text-center py-10">
                            <div class="text-gray-900 font-semibold">Belum ada proposal</div>
                            <div class="text-sm text-gray-600 mt-1">Proposal yang kamu ajukan / review akan muncul di sini.</div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                        <th class="px-4 py-3">Proposal</th>
                                        <th class="px-4 py-3">Bidang / Sie</th>
                                        <th class="px-4 py-3">Pengaju</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3">Tahap</th>
                                        {{-- <th class="px-4 py-3">Tanggal</th> --}}
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y" id="proposalTableBody">
                                    @foreach ($proposals as $p)
                                        @php
                                            $bidangName = $p->bidang?->nama_bidang ?? '-';
                                            $sieName = $p->sie?->nama_sie ?? '-';
                                            $pengajuName = $p->pengaju?->name ?? '-';

                                            $statusLabel = match($p->status) {
                                                'menunggu_ketua_bidang' => 'Menunggu Ketua Bidang',
                                                'dpp_harian'            => 'DPP Harian (View-only)',
                                                'menunggu_romo'         => 'Menunggu Romo',
                                                'approved'              => 'Disetujui',
                                                'ditolak'               => 'Ditolak',
                                                default                 => ucfirst(str_replace('_',' ', $p->status ?? '-')),
                                            };

                                            $stageLabel = match($p->stage) {
                                                'sie'         => 'Sie (Pengaju)',
                                                'ketua_bidang' => 'Ketua Bidang',
                                                'dpp_harian'   => 'DPP Harian',
                                                'romo'         => 'Romo',
                                                'bendahara'    => 'Bendahara',
                                                default        => ucfirst(str_replace('_',' ', $p->stage ?? '-')),
                                            };

                                            $statusClass = match($p->status) {
                                                'approved'              => 'bg-green-50 text-green-700',
                                                'ditolak'               => 'bg-red-50 text-red-700',
                                                'menunggu_romo'         => 'bg-yellow-50 text-yellow-800',
                                                'dpp_harian'            => 'bg-blue-50 text-blue-700',
                                                'menunggu_ketua_bidang' => 'bg-gray-100 text-gray-700',
                                                default                 => 'bg-gray-100 text-gray-700',
                                            };

                                            $stageClass = match($p->stage) {
                                                'bendahara'   => 'bg-green-50 text-green-700',
                                                'romo'        => 'bg-yellow-50 text-yellow-800',
                                                'dpp_harian'  => 'bg-blue-50 text-blue-700',
                                                'ketua_bidang'=> 'bg-indigo-50 text-indigo-700',
                                                'sie'         => 'bg-gray-100 text-gray-700',
                                                default       => 'bg-gray-100 text-gray-700',
                                            };

                                            $searchHaystack = strtolower(
                                                ($p->judul ?? '').' '.
                                                ($bidangName).' '.
                                                ($sieName).' '.
                                                ($pengajuName).' '.
                                                ($p->status ?? '').' '.
                                                ($p->stage ?? '').' '.
                                                ($p->proposal_no ?? '')
                                            );

                                            $files = collect($p->files ?? []);
                                            $fileCount = $files->count();

                                            // JSON untuk SweetAlert (multi-file)
                                            $filesJson = $files->map(fn($f) => [
                                                'id' => $f->id,
                                                'name' => $f->original_name,
                                            ])->values()->toJson();
                                        @endphp

                                        <tr class="hover:bg-gray-50 proposal-row" data-search="{{ $searchHaystack }}">

                                            {{-- Nama Proposal --}}
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-gray-900">{{ $p->judul }}</div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Lampiran: {{ $fileCount }} file
                                                    @if(!empty($p->proposal_no))
                                                        • No: <span class="font-semibold text-gray-700">{{ $p->proposal_no }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-600 mt-1">
                                                    {{ \Illuminate\Support\Str::limit($p->tujuan ?? '-', 80) }}
                                                </div>
                                            </td>
                                            {{-- Nama Proposal --}}

                                            {{-- Nama Bidang --}}
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">{{ $bidangName }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $sieName }}</div>
                                            </td>
                                            {{-- Nama Bidang --}}

                                            {{-- Nama Pengaju --}}
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">{{ $pengajuName }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $p->pengaju?->email ?? '-' }}</div>
                                            </td>
                                            {{-- Nama Pengaju --}}

                                            {{-- Status --}}
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $statusClass }}">
                                                    {{ $statusLabel }}
                                                </span>

                                                @if($p->status === 'dpp_harian' && $p->dpp_harian_until)
                                                    <div class="text-xs text-gray-500 mt-2">
                                                        Sampai: {{ $p->dpp_harian_until->format('d-m-Y | H:i') }}
                                                    </div>
                                                @endif
                                            </td>
                                            {{-- Status --}}

                                            {{-- Tahap --}}
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $stageClass }}">
                                                    {{ $stageLabel }}
                                                </span>
                                            </td>
                                            {{-- Tahap --}}

                                            {{-- Tanggal --}}
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">{{ optional($p->created_at)->format('d-m-Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ optional($p->created_at)->format('H:i') }}</div>
                                            </td>
                                            {{-- Tanggal --}}

                                            {{-- AKSI (KONSISTEN: Preview, Download, + Folder) --}}
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center gap-2 flex-wrap">

                                                    {{-- PREVIEW --}}
                                                    @if($fileCount === 1)
                                                        @php
                                                            $onlyFile = $files->first();
                                                        @endphp
                                                        <a href="{{ route('proposals.files.preview', ['proposal' => $p->id, 'file' => $onlyFile->id]) }}"
                                                           target="_blank"
                                                           class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                            Preview
                                                        </a>
                                                    @else
                                                        <button type="button"
                                                                class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm"
                                                                data-proposal-id="{{ $p->id }}"
                                                                data-files='{!! $filesJson !!}'
                                                                data-action="preview"
                                                                onclick="openProposalFileAction(this)">
                                                            Preview
                                                        </button>
                                                    @endif

                                                    {{-- DOWNLOAD --}}
                                                    @if($fileCount === 1)
                                                        <a href="{{ route('proposals.files.download', ['proposal' => $p->id, 'file' => $onlyFile->id]) }}"
                                                           class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                            Download
                                                        </a>
                                                    @else
                                                        <button type="button"
                                                                class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm"
                                                                data-proposal-id="{{ $p->id }}"
                                                                data-files='{!! $filesJson !!}'
                                                                data-action="download"
                                                                onclick="openProposalFileAction(this)">
                                                            Download
                                                        </button>
                                                    @endif

                                                    {{-- + Folder --}}
                                                    @can('files.manage')
                                                        @if(($folders ?? collect())->isEmpty())
                                                            <a href="{{ route('folders.index') }}"
                                                               class="px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-sm">
                                                                Buat Folder dulu
                                                            </a>
                                                        @else
                                                            <details class="relative">
                                                                <summary class="list-none cursor-pointer px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm inline-flex items-center gap-2">
                                                                    + Folder
                                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                        <path d="M6 9l6 6 6-6"/>
                                                                    </svg>
                                                                </summary>

                                                                <div class="absolute right-0 mt-2 w-64 bg-white border rounded-lg shadow-lg p-3 z-20">
                                                                    <form method="POST" onsubmit="return submitToFolderProposal(this);">
                                                                        @csrf

                                                                        <input type="hidden" name="source" value="proposal_file">

                                                                        {{-- 1 file = hidden; multi file = select (tanpa teks "File: ...") --}}
                                                                        @if($fileCount === 1)
                                                                            <input type="hidden" name="source_id" value="{{ $files->first()->id }}">
                                                                        @else
                                                                            <label class="text-xs text-gray-600">Pilih file proposal</label>
                                                                            <select name="source_id" class="mt-1 w-full border-gray-300 rounded text-sm">
                                                                                <option value="">Pilih file...</option>
                                                                                @foreach($files as $pf)
                                                                                    <option value="{{ $pf->id }}">{{ $pf->original_name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        @endif

                                                                        <label class="text-xs text-gray-600 mt-3 block">Pilih folder</label>
                                                                        <select name="folder_id" class="mt-1 w-full border-gray-300 rounded text-sm">
                                                                            <option value="">Pilih folder...</option>
                                                                            @foreach($folders as $f)
                                                                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                                                                            @endforeach
                                                                        </select>

                                                                        <button type="submit"
                                                                                class="mt-3 w-full px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
                                                                            Simpan ke Folder
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </details>
                                                        @endif
                                                    @endcan

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div id="proposalNoResult" class="hidden text-center py-10">
                            <div class="text-gray-900 font-semibold">Proposal tidak ditemukan</div>
                            <div class="text-sm text-gray-600 mt-1">Coba kata kunci lain.</div>
                        </div>

                        <div class="mt-4">
                            {{ $proposals->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @once
        <script>
            // Search (client-side)
            const proposalSearch = document.getElementById('proposalSearch');
            const proposalRows = document.querySelectorAll('.proposal-row');
            const proposalNoResult = document.getElementById('proposalNoResult');

            if (proposalSearch) {
                proposalSearch.addEventListener('input', () => {
                    const q = (proposalSearch.value || '').toLowerCase().trim();
                    let visible = 0;

                    proposalRows.forEach(row => {
                        const hay = row.getAttribute('data-search') || '';
                        const show = hay.includes(q);
                        row.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });

                    if (proposalNoResult) proposalNoResult.classList.toggle('hidden', visible !== 0);
                });
            }

            /**
             * Multi-file Preview/Download: pilih file lewat SweetAlert lalu redirect ke route yang benar.
             * NOTE: route butuh 2 parameter: {proposal} dan {file}
             */
            function openProposalFileAction(btn) {
                const action = btn.getAttribute('data-action'); // preview | download
                const proposalId = btn.getAttribute('data-proposal-id');
                let files = [];

                try {
                    files = JSON.parse(btn.getAttribute('data-files') || '[]');
                } catch (e) {
                    files = [];
                }

                if (!files.length) {
                    Swal.fire('Tidak ada file lampiran', '', 'warning');
                    return;
                }

                // bikin opsi select
                const inputOptions = {};
                files.forEach(f => {
                    inputOptions[String(f.id)] = f.name;
                });

                Swal.fire({
                    title: (action === 'preview') ? 'Pilih file untuk Preview' : 'Pilih file untuk Download',
                    input: 'select',
                    inputOptions,
                    inputPlaceholder: 'Pilih file...',
                    showCancelButton: true,
                    confirmButtonText: 'Lanjut',
                    cancelButtonText: 'Batal',
                    inputValidator: (value) => {
                        if (!value) return 'Pilih file dulu';
                    }
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    const fileId = result.value;

                    // URL template dari Laravel (paling aman)
                    const previewTpl = @json(route('proposals.files.preview', ['proposal' => '__PROPOSAL__', 'file' => '__FILE__']));
                    const downloadTpl = @json(route('proposals.files.download', ['proposal' => '__PROPOSAL__', 'file' => '__FILE__']));

                    const url = (action === 'preview')
                        ? previewTpl.replace('__PROPOSAL__', proposalId).replace('__FILE__', fileId)
                        : downloadTpl.replace('__PROPOSAL__', proposalId).replace('__FILE__', fileId);

                    if (action === 'preview') {
                        window.open(url, '_blank');
                    } else {
                        window.location.href = url;
                    }
                });
            }

            /**
             * Folder submit (pola sama seperti Dokumen)
             */
            function submitToFolderProposal(form) {
                const folderId = form.querySelector('[name="folder_id"]').value;

                // kalau multi-file: wajib pilih source_id
                const sourceIdEl = form.querySelector('[name="source_id"]');
                if (sourceIdEl && !sourceIdEl.value) {
                    Swal.fire('Pilih file proposal dulu', '', 'warning');
                    return false;
                }

                if (!folderId) {
                    Swal.fire('Pilih folder dulu', '', 'warning');
                    return false;
                }

                form.action = `/folders/${folderId}/items`;
                return true;
            }

            window.openProposalFileAction = openProposalFileAction;
            window.submitToFolderProposal = submitToFolderProposal;
        </script>
    @endonce
</x-app-layout>