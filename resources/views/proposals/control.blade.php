{{-- resources/views/proposals/control.blade.php --}}
@php use Illuminate\Support\Facades\Gate; @endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengajuan Proposal</h2>
            <p class="text-sm text-gray-500">
                Ajukan, review, dan proses persetujuan proposal sesuai alur (Sie → Ketua Bidang → DPP (Ketua DPP & Sekretaris 1-2)).
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
                                        <th class="px-4 py-3">No. Proposal</th>
                                        <th class="px-4 py-3">Bidang / Sie</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3">Tahap</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y" id="proposalTableBody">
                                    @foreach ($proposals as $p)
                                        @php
                                            $bidangName = $p->bidang?->nama_bidang ?? '-';
                                            $sieName = $p->sie?->nama_sie ?? '-';
                                            $pengajuName = $p->pengaju?->name ?? '-';

                                            // ✅ STATUS (brief baru)
                                            $statusLabel = match($p->status) {
                                                'menunggu_ketua_bidang' => 'Menunggu Ketua Bidang',
                                                'menunggu_romo'         => 'Menunggu Persetujuan DPP',
                                                'approved'              => 'Disetujui',
                                                'revisi'                => 'Revisi',
                                                default                 => ucfirst(str_replace('_',' ', $p->status ?? '-')),
                                            };

                                            // ✅ STAGE (brief baru)
                                            $stageLabel = match($p->stage) {
                                                'sie'         => 'Sie (Pengaju)',
                                                'ketua_bidang' => 'Ketua Bidang',
                                                'romo'         => 'DPP (Ketua / Sekretaris 1-2)',
                                                'bendahara'    => 'Bendahara',
                                                default        => ucfirst(str_replace('_',' ', $p->stage ?? '-')),
                                            };

                                            $statusClass = match($p->status) {
                                                'approved'              => 'bg-green-50 text-green-700',
                                                'revisi'                => 'bg-red-50 text-red-700',
                                                'menunggu_romo'         => 'bg-yellow-50 text-yellow-800',
                                                'menunggu_ketua_bidang' => 'bg-gray-100 text-gray-700',
                                                default                 => 'bg-gray-100 text-gray-700',
                                            };

                                            $stageClass = match($p->stage) {
                                                'bendahara'   => 'bg-green-50 text-green-700',
                                                'romo'        => 'bg-yellow-50 text-yellow-800',
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

                                            $filesArr = collect($p->files ?? [])->map(fn($f) => [
                                                'id' => $f->id,
                                                'name' => $f->original_name,
                                            ])->values()->all();
                                        @endphp

                                        {{-- Kolom Proposal --}}
                                        <tr class="hover:bg-gray-50 proposal-row" data-search="{{ $searchHaystack }}">

                                            {{-- Nama Proposal --}}
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-gray-900">{{ $p->judul }}</div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ \Illuminate\Support\Str::limit($p->tujuan ?? '-', 80) }}
                                                </div>
                                                {{-- <div class="text-xs text-gray-600 mt-1">
                                                    Lampiran: {{ $p->files?->count() ?? 0 }} file
                                                </div> --}}
                                            </td>
                                            {{-- End Nama Proposal --}}

                                            {{-- Nomor Proposal --}}
                                            <td class="px-4 py-3">
                                                @if(!empty($p->proposal_no))
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800 font-semibold">
                                                        {{ $p->proposal_no }}
                                                    </span>
                                                @else
                                                    <span class="text-sm text-gray-500">
                                                        -
                                                    </span>
                                                @endif
                                            </td>
                                            {{-- End Nomor Proposal --}}

                                            {{-- Sie & Bidang Pengaju --}}
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">{{ $sieName }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $bidangName }}</div>
                                            </td>
                                            {{-- End Sie & Bidang Pengaju --}}

                                            {{-- Status --}}
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $statusClass }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                            {{-- End Status --}}

                                            {{-- Tahap --}}
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $stageClass }}">
                                                    {{ $stageLabel }}
                                                </span>
                                            </td>
                                            {{-- End Tahap --}}

                                            {{-- Aksi --}}
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-center gap-2 flex-wrap">

                                                    @php
                                                        $u = auth()->user();
                                                        $canSubmitLpj = $u && $u->hasRole('ketua_sie') && (int)$p->created_by === (int)$u->id && $p->status === 'approved';
                                                    @endphp

                                                    <a href="{{ route('proposals.show', $p) }}"
                                                       class="px-3 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 text-sm">
                                                        Detail
                                                    </a>

                                                    @php
                                                        $u = auth()->user();

                                                        // cek apakah proposal ini punya LPJ
                                                        // (pakai relation yang sudah kamu eager load latestLpj sebagai indikator cepat)
                                                        $hasAnyLpj = !is_null($p->latestLpj);

                                                        // aturan tampil tombol "Lihat LPJ":
                                                        // - kalau ada LPJ minimal 1
                                                        // - dan user role reviewer (ketua bidang / dpp / bendahara / superadmin) atau pemilik proposal
                                                        $canOpenLpjList = $hasAnyLpj && $u && (
                                                            $u->hasRole('super_admin')
                                                            || $u->hasRole('ketua_bidang')
                                                            || $u->hasRole('bendahara')
                                                            || $u->hasAnyRole(['ketua','wakil_ketua','sekretaris'])
                                                            || ((int)$p->created_by === (int)$u->id) // pemilik proposal
                                                        );
                                                    @endphp

                                                    @if($canOpenLpjList)
                                                        <a href="{{ route('lpj.by_proposal', $p) }}"
                                                        class="p-2 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100"
                                                        title="Lihat semua LPJ">
                                                            {{-- icon dokumen/list --}}
                                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M8 6h13M8 12h13M8 18h13"/>
                                                                <path d="M3 6h.01M3 12h.01M3 18h.01"/>
                                                            </svg>
                                                        </a>
                                                    @endif

                                                    @if($canSubmitLpj)
                                                    <a href="{{ route('lpj.create', $p) }}"
                                                        class="p-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100"
                                                        title="Submit LPJ">
                                                        {{-- icon upload --}}
                                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                            <path d="M7 10l5-5 5 5"/>
                                                            <path d="M12 5v14"/>
                                                        </svg>
                                                    </a>
                                                    @endif

                                                    {{-- Fitur Manajemen File khusus Sekretaris --}}
                                                    <div class="flex flex-row-reverse items-center gap-2 flex-wrap">
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

                                                                    <div class="absolute right-0 mt-2 w-72 bg-white border rounded-lg shadow-lg p-3 z-20">
                                                                        <form method="POST" onsubmit="return submitToFolderProposal(this);">
                                                                            @csrf
                                                                            <input type="hidden" name="source" value="proposal_file">

                                                                            @if(($p->files?->count() ?? 0) === 1)
                                                                                <input type="hidden" name="source_id" value="{{ $p->files->first()->id }}">
                                                                            @else
                                                                                <label class="text-xs text-gray-600">Pilih file proposal</label>
                                                                                <select name="source_id" class="mt-1 w-full border-gray-300 rounded text-sm">
                                                                                    <option value="">Pilih file...</option>
                                                                                    @foreach($p->files as $pf)
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
                                                    {{-- End fitur Manajemen File --}}

                                                </div>
                                            </td>
                                            {{-- End Aksi --}}
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

            @if (session('status'))
                Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('status')) });
            @endif
            @if (session('error'))
                Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: @json(session('error')) });
            @endif

            function submitToFolderProposal(form) {
                const folderId = form.querySelector('[name="folder_id"]').value;
                const sourceIdEl = form.querySelector('[name="source_id"]');
                const sourceId = sourceIdEl ? sourceIdEl.value : '';

                if (sourceIdEl && !sourceId) {
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
            window.submitToFolderProposal = submitToFolderProposal;
        </script>
    @endonce
</x-app-layout>
