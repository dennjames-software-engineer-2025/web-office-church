{{-- resources/views/lpj/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail LPJ</h2>
            <p class="text-sm text-gray-500">Lihat detail LPJ dan lampiran file.</p>
        </div>
    </x-slot>

    @php
        $statusLabel = fn($s) => match($s) {
            'menunggu_ketua_bidang' => 'Menunggu Ketua Bidang',
            'menunggu_romo'         => 'Menunggu Persetujuan DPP',
            'approved'              => 'Disetujui',
            'revisi'                => 'Perlu Perbaikan',
            default                 => ucfirst(str_replace('_',' ', $s ?? '-')),
        };

        $stageLabel = fn($st) => match($st) {
            'ketua_bidang' => 'Ketua Bidang',
            'romo'         => 'DPP (Forum)',
            'bendahara'    => 'Bendahara',
            'sie'          => 'Sie (Pengaju)',
            default        => ucfirst(str_replace('_',' ', $st ?? '-')),
        };

        $badgeClass = fn($s) => match($s) {
            'approved'              => 'bg-green-50 text-green-700 border-green-200',
            'revisi'                => 'bg-red-50 text-red-700 border-red-200',
            'menunggu_romo'         => 'bg-yellow-50 text-yellow-800 border-yellow-200',
            'menunggu_ketua_bidang' => 'bg-gray-50 text-gray-700 border-gray-200',
            default                 => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    @endphp

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">

                {{-- HEADER CARD (konsisten seperti Proposal) --}}
                <div class="p-6 border-b bg-gray-50">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <a href="{{ route('lpj.by_proposal', $proposal) }}"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                            ← Kembali
                        </a>

                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs border {{ $badgeClass($lpj->status) }}">
                                {{ $statusLabel($lpj->status) }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs border bg-gray-50 text-gray-700 border-gray-200">
                                Tahap: {{ $stageLabel($lpj->stage) }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-5">
                        <div class="text-sm text-gray-500">Proposal</div>
                        <div class="text-2xl font-semibold text-gray-900 break-words">{{ $proposal->judul }}</div>

                        {{-- ✅ nomor proposal tampil jelas di LPJ --}}
                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
                            <span class="inline-flex items-center px-3 py-1 rounded-full border bg-gray-50 text-gray-700 border-gray-200">
                                No Proposal: <span class="ml-1 font-semibold">{{ $proposal->proposal_no ?? '-' }}</span>
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full border bg-gray-50 text-gray-700 border-gray-200">
                                Dikirim: {{ optional($lpj->created_at)->format('d-m-Y | H:i') }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full border bg-gray-50 text-gray-700 border-gray-200">
                                Tanggal LPJ: {{ $lpj->lpj_date ? $lpj->lpj_date->format('d-m-Y') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">

                    {{-- INFO LPJ --}}
                    <div class="rounded-lg border bg-white p-4 space-y-4">
                        <div class="text-xs text-gray-500">Nama LPJ</div>
                        <div class="text-xl font-semibold text-gray-900 break-words">
                            {{ $lpj->lpj_name ?: ('LPJ #' . $lpj->id) }}
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                            <div class="rounded-lg border p-4">
                                <div class="text-gray-500">Bidang</div>
                                <div class="font-semibold text-gray-900 mt-1">{{ $lpj->bidang?->nama_bidang ?? '-' }}</div>
                            </div>

                            <div class="rounded-lg border p-4">
                                <div class="text-gray-500">Sie</div>
                                <div class="font-semibold text-gray-900 mt-1">{{ $lpj->sie?->nama_sie ?? '-' }}</div>
                            </div>

                            <div class="rounded-lg border p-4">
                                <div class="text-gray-500">Pengaju</div>
                                <div class="font-semibold text-gray-900 mt-1">{{ $lpj->pengaju?->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $lpj->pengaju?->email ?? '-' }}</div>
                            </div>
                        </div>

                        {{-- AKSI APPROVAL --}}
                        <div class="flex gap-2 flex-wrap">
                            @can('approveKetuaBidang', $lpj)
                                <form method="POST" action="{{ route('lpj.approve_ketua_bidang', [$proposal, $lpj]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-sm font-medium">
                                        ✅ Setujui (Ketua Bidang)
                                    </button>
                                </form>
                            @endcan

                            @can('approveFinal', $lpj)
                                <form method="POST" action="{{ route('lpj.approve_final', [$proposal, $lpj]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-sm font-medium">
                                        ✅ Setujui Final (DPP)
                                    </button>
                                </form>

                                <button type="button"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium"
                                        onclick="document.getElementById('reject-modal').classList.remove('hidden')">
                                    ✍ Perbaiki
                                </button>
                            @endcan
                        </div>

                        @if(!empty($lpj->notes))
                            <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                                <div class="text-sm font-semibold text-yellow-800">Notes / Comment</div>
                                <div class="text-sm text-gray-900 whitespace-pre-line mt-2">{{ $lpj->notes }}</div>
                            </div>
                        @endif

                        @if($lpj->status === 'revisi' && !empty($lpj->reject_reason))
                            <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                                <div class="text-sm font-semibold text-red-700">Perbaikan</div>
                                <div class="text-sm text-gray-900 whitespace-pre-line mt-2">{{ $lpj->reject_reason }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- LAMPIRAN --}}
                    <div class="rounded-lg border bg-white p-4 space-y-3">
                        <div class="text-sm font-semibold text-gray-900">Lampiran LPJ</div>

                        @if(($lpj->files?->count() ?? 0) === 0)
                            <div class="text-sm text-gray-600">Belum ada file.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                            <th class="px-4 py-3 w-16">No</th>
                                            <th class="px-4 py-3">Nama File</th>
                                            <th class="px-4 py-3 w-40">Ukuran</th>
                                            <th class="px-4 py-3 text-center w-[420px]">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach($lpj->files as $i => $f)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-gray-700">{{ $i + 1 }}</td>

                                                <td class="px-4 py-3">
                                                    <div class="text-sm text-gray-900 font-medium">{{ $f->original_name }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">{{ $f->mime_type ?? '-' }}</div>
                                                </td>

                                                <td class="px-4 py-3 text-sm text-gray-700">
                                                    @php $kb = $f->file_size ? number_format($f->file_size / 1024, 2) : null; @endphp
                                                    {{ $kb ? $kb.' KB' : '-' }}
                                                </td>

                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center gap-2 flex-wrap">

                                                        <a href="{{ route('lpj.files.preview', [$proposal, $lpj, $f]) }}"
                                                           target="_blank"
                                                           class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                            Preview
                                                        </a>

                                                        @can('downloadFile', $lpj)
                                                            <a href="{{ route('lpj.files.download', [$proposal, $lpj, $f]) }}"
                                                               class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                                Download
                                                            </a>
                                                        @endcan

                                                        {{-- ✅ + Folder untuk sekretaris (ikut pola Proposal) --}}
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

                                                                            {{-- ⚠️ penting: source beda agar backend tahu ini file LPJ --}}
                                                                            <input type="hidden" name="source" value="lpj_file">
                                                                            <input type="hidden" name="source_id" value="{{ $f->id }}">

                                                                            <label class="text-xs text-gray-600 mt-1 block">Pilih folder</label>
                                                                            <select name="folder_id" class="mt-1 w-full border-gray-300 rounded text-sm">
                                                                                <option value="">Pilih folder...</option>
                                                                                @foreach($folders as $fo)
                                                                                    <option value="{{ $fo->id }}">{{ $fo->name }}</option>
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
                        @endif
                    </div>

                    {{-- MODAL REJECT --}}
                    @can('rejectFinal', $lpj)
                        <div id="reject-modal" class="hidden fixed inset-0 z-50">
                            <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('reject-modal').classList.add('hidden')"></div>

                            <div class="relative max-w-xl mx-auto mt-24 bg-white rounded-lg shadow p-6">
                                <div class="text-lg font-semibold text-gray-900">Perbaiki LPJ</div>
                                <p class="text-sm text-gray-600 mt-1">Isi alasan perbaikan agar pengaju bisa revisi.</p>

                                <form method="POST" action="{{ route('lpj.reject_final', [$proposal, $lpj]) }}" class="mt-4 space-y-3">
                                    @csrf
                                    @method('PATCH')

                                    <textarea name="reject_reason" rows="5"
                                              class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                              placeholder="Contoh: Bukti pembayaran belum lengkap, mohon lampirkan nota...">{{ old('reject_reason') }}</textarea>
                                    @error('reject_reason') <div class="text-sm text-red-600">{{ $message }}</div> @enderror

                                    <div class="flex justify-end gap-2">
                                        <button type="button"
                                                class="px-4 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm"
                                                onclick="document.getElementById('reject-modal').classList.add('hidden')">
                                            Batal
                                        </button>
                                        <button type="submit"
                                                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium">
                                            Kirim Perbaikan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
