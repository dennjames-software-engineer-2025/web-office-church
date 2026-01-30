{{-- resources/views/lpj/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail LPJ</h2>
            <p class="text-sm text-gray-500">Lihat detail LPJ dan lakukan aksi sesuai tahapan.</p>
        </div>
    </x-slot>

    @php
        // label status & stage biar ramah lansia
        $statusLabel = match($lpj->status) {
            'menunggu_ketua_bidang' => 'Menunggu Ketua Bidang',
            'menunggu_romo'         => 'Menunggu DPP Harian',
            'approved'              => 'Disetujui',
            'revisi'                => 'Perlu Perbaikan',
            default                 => ucfirst(str_replace('_',' ', $lpj->status ?? '-')),
        };

        $stageLabel = match($lpj->stage) {
            'sie'         => 'Sie (Pengaju)',
            'ketua_bidang' => 'Ketua Bidang',
            'romo'         => 'DPP Harian (Ketua/Sekretaris 1-2)',
            'bendahara'    => 'Bendahara',
            default        => ucfirst(str_replace('_',' ', $lpj->stage ?? '-')),
        };

        $statusClass = match($lpj->status) {
            'approved'              => 'bg-green-50 text-green-700 border-green-200',
            'revisi'                => 'bg-red-50 text-red-700 border-red-200',
            'menunggu_romo'         => 'bg-yellow-50 text-yellow-800 border-yellow-200',
            'menunggu_ketua_bidang' => 'bg-gray-50 text-gray-700 border-gray-200',
            default                 => 'bg-gray-50 text-gray-700 border-gray-200',
        };

        $stageClass = match($lpj->stage) {
            'bendahara'   => 'bg-green-50 text-green-700 border-green-200',
            'romo'        => 'bg-yellow-50 text-yellow-800 border-yellow-200',
            'ketua_bidang'=> 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'sie'         => 'bg-gray-50 text-gray-700 border-gray-200',
            default       => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    @endphp

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Header ringkas: Kembali + Status --}}
            <div class="bg-white shadow sm:rounded-lg p-5">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('lpjs.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                            ← Kembali
                        </a>
                        <a href="{{ route('proposals.show', $proposal) }}"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                            Lihat Proposal
                        </a>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs border {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs border {{ $stageClass }}">
                            Tahap: {{ $stageLabel }}
                        </span>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="text-sm text-gray-500">LPJ</div>
                    <div class="text-lg font-semibold text-gray-900">LPJ #{{ $lpj->id }}</div>
                    <div class="text-xs text-gray-500 mt-1">
                        Dikirim: {{ optional($lpj->created_at)->format('d-m-Y | H:i') }}
                    </div>
                </div>
            </div>

            {{-- Info Proposal --}}
            <div class="bg-white shadow sm:rounded-lg p-5">
                <div class="text-sm font-semibold text-gray-900">Informasi Proposal</div>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div class="rounded-lg border p-4">
                        <div class="text-gray-500">Judul Proposal</div>
                        <div class="font-semibold text-gray-900 mt-1">{{ $proposal->judul }}</div>
                    </div>
                    <div class="rounded-lg border p-4">
                        <div class="text-gray-500">No Proposal</div>
                        <div class="font-semibold text-gray-900 mt-1">{{ $proposal->proposal_no ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Info LPJ --}}
            <div class="bg-white shadow sm:rounded-lg p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
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

                @if(!empty($lpj->notes))
                    <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                        <div class="text-sm font-semibold text-yellow-800">Notes / Comment</div>
                        <div class="text-sm text-gray-900 whitespace-pre-line mt-2">{{ $lpj->notes }}</div>
                    </div>
                @endif

                @if($lpj->status === 'revisi' && !empty($lpj->reject_reason))
                    <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                        <div class="text-sm font-semibold text-red-700">Catatan Perbaikan</div>
                        <div class="text-sm text-gray-900 whitespace-pre-line mt-2">{{ $lpj->reject_reason }}</div>
                    </div>
                @endif
            </div>

            {{-- Lampiran LPJ --}}
            <div class="bg-white shadow sm:rounded-lg p-5 space-y-3">
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
                                    <th class="px-4 py-3 text-center w-56">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($lpj->files as $i => $f)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm text-gray-900 font-medium">{{ $f->original_name }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            @php $kb = $f->file_size ? number_format($f->file_size / 1024, 2) : null; @endphp
                                            {{ $kb ? $kb.' KB' : '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-2 flex-wrap">
                                                <a href="{{ route('lpj.files.preview', [$proposal, $lpj, $f]) }}" target="_blank"
                                                   class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                    Preview
                                                </a>

                                                @can('downloadFile', $lpj)
                                                    <a href="{{ route('lpj.files.download', [$proposal, $lpj, $f]) }}"
                                                       class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                        Download
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @php
                        $isBendahara = auth()->check() && auth()->user()->hasRole('bendahara');
                    @endphp

                    @if($isBendahara && $lpj->status !== 'approved' && !Gate::allows('downloadFile', $lpj))
                        <div class="mt-3 rounded-lg bg-yellow-50 border border-yellow-200 p-3 text-sm text-yellow-900">
                            <div class="font-semibold">Catatan</div>
                            <div class="mt-1">
                                File hanya bisa diunduh ketika LPJ sudah disetujui final oleh Ketua DPP / Sekretaris 1-2.
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- AKSI LPJ (ini yang sebelumnya belum ada) --}}
            <div class="bg-white shadow sm:rounded-lg p-5 space-y-4">
                <div class="text-sm font-semibold text-gray-900">Aksi</div>

                {{-- 1) Ketua Bidang Approve --}}
                @can('approveKetuaBidang', $lpj)
                    @if($lpj->stage === 'ketua_bidang' && $lpj->status === 'menunggu_ketua_bidang')
                        <form method="POST" action="{{ route('lpj.approve_ketua_bidang', [$proposal, $lpj]) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium">
                                Setujui (Ketua Bidang)
                            </button>
                        </form>
                    @endif
                @endcan

                {{-- 2) Final Approve/Reject (DPP Harian) --}}
                @if($lpj->stage === 'romo' && $lpj->status === 'menunggu_romo')
                    <div class="rounded-lg border bg-gray-50 p-4 space-y-3">
                        <div class="text-sm font-semibold text-gray-900">Persetujuan DPP Harian</div>

                        <div class="flex flex-wrap gap-2">
                            @can('approveFinal', $lpj)
                                <form method="POST" action="{{ route('lpj.approve_final', [$proposal, $lpj]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium">
                                        Setujui
                                    </button>
                                </form>
                            @endcan

                            @can('rejectFinal', $lpj)
                                <details class="w-full sm:w-auto">
                                    <summary class="list-none cursor-pointer px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium inline-flex items-center gap-2">
                                        Perbaiki
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M6 9l6 6 6-6"/>
                                        </svg>
                                    </summary>

                                    <div class="mt-2 rounded-lg border bg-white p-4">
                                        <form method="POST" action="{{ route('lpj.reject_final', [$proposal, $lpj]) }}" class="space-y-2">
                                            @csrf
                                            @method('PATCH')

                                            <label class="text-xs text-gray-600">Catatan Perbaikan</label>
                                            <textarea name="reject_reason" rows="4"
                                                    class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                                    placeholder="Tulis catatan perbaikan yang jelas..." required>{{ old('reject_reason') }}</textarea>

                                            <button type="submit"
                                                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium">
                                                Kirim Perbaikan
                                            </button>
                                        </form>
                                    </div>
                                </details>
                            @endcan
                        </div>
                    </div>
                @endif

                {{-- 3) Notes (Ketua DPP / Sekretaris 1-2) --}}
                @can('addNotes', $lpj)
                    @if($lpj->stage === 'romo')
                        <div class="rounded-lg border bg-gray-50 p-4">
                            <div class="text-sm font-semibold text-gray-900">Tambahkan Notes</div>

                            <form method="POST" action="{{ route('lpj.add_notes', [$proposal, $lpj]) }}" class="mt-3 space-y-2">
                                @csrf
                                @method('PATCH')

                                <textarea name="notes" rows="4"
                                        class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                        placeholder="Tulis notes yang jelas...">{{ old('notes') }}</textarea>

                                <button type="submit"
                                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
                                    Simpan Notes
                                </button>
                            </form>
                        </div>
                    @endif
                @endcan
            </div>

        </div>
    </div>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('status'))
            Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('status')) });
        @endif
        @if (session('error'))
            Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: @json(session('error')) });
        @endif
    </script>
</x-app-layout>
