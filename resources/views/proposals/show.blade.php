<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Proposal</h2>
            <p class="text-sm text-gray-500">Lihat informasi lengkap proposal dan lakukan aksi sesuai tahapan.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">

                {{-- Header Card --}}
                @php
                    $statusLabel = match($proposal->status) {
                        'menunggu_ketua_bidang' => 'Menunggu Ketua Bidang',
                        'dpp_harian'            => 'DPP Harian (View-only)',
                        'menunggu_romo'         => 'Menunggu Romo',
                        'approved'              => 'Disetujui',
                        'revisi'                => 'Revisi',
                        default                 => ucfirst(str_replace('_',' ', $proposal->status ?? '-')),
                    };

                    $stageLabel = match($proposal->stage) {
                        'sie'         => 'Sie (Pengaju)',
                        'ketua_bidang' => 'Ketua Bidang',
                        'dpp_harian'   => 'DPP Harian',
                        'romo'         => 'Romo',
                        'bendahara'    => 'Bendahara',
                        default        => ucfirst(str_replace('_',' ', $proposal->stage ?? '-')),
                    };

                    $statusClass = match($proposal->status) {
                        'approved'              => 'bg-green-50 text-green-700 border-green-200',
                        'revisi'                => 'bg-red-50 text-red-700 border-red-200',
                        'menunggu_romo'         => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                        'dpp_harian'            => 'bg-blue-50 text-blue-700 border-blue-200',
                        'menunggu_ketua_bidang' => 'bg-gray-50 text-gray-700 border-gray-200',
                        default                 => 'bg-gray-50 text-gray-700 border-gray-200',
                    };

                    $stageClass = match($proposal->stage) {
                        'bendahara'   => 'bg-green-50 text-green-700 border-green-200',
                        'romo'        => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                        'dpp_harian'  => 'bg-blue-50 text-blue-700 border-blue-200',
                        'ketua_bidang'=> 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'sie'         => 'bg-gray-50 text-gray-700 border-gray-200',
                        default       => 'bg-gray-50 text-gray-700 border-gray-200',
                    };
                @endphp

                <div class="p-6 border-b bg-gray-50">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('proposals.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                                ← Kembali
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

                    <div class="mt-5">
                        <div class="text-sm text-gray-500">Judul</div>
                        <div class="text-2xl font-semibold text-gray-900">{{ $proposal->judul }}</div>

                        <div class="text-xs text-gray-500 mt-2 flex flex-wrap gap-2">
                            <span>Dibuat: {{ optional($proposal->created_at)->format('d-m-Y | H:i') }}</span>
                            @if(!empty($proposal->proposal_no))
                                <span>• No Proposal: <span class="font-semibold text-gray-700">{{ $proposal->proposal_no }}</span></span>
                            @endif
                        </div>

                        @if($proposal->status === 'dpp_harian' && $proposal->dpp_harian_until)
                            <div class="mt-4 rounded-lg bg-blue-50 border border-blue-100 px-4 py-3 inline-block">
                                <div class="text-xs text-blue-700 font-semibold">DPP Harian sampai</div>
                                <div class="text-sm text-blue-900 font-medium mt-1">
                                    {{ $proposal->dpp_harian_until->format('d-m-Y | H:i') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-6">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="rounded-lg border bg-white p-4">
                            <div class="text-gray-500">Bidang</div>
                            <div class="text-gray-900 font-semibold mt-1">{{ $proposal->bidang?->nama_bidang ?? '-' }}</div>
                        </div>

                        <div class="rounded-lg border bg-white p-4">
                            <div class="text-gray-500">Sie</div>
                            <div class="text-gray-900 font-semibold mt-1">{{ $proposal->sie?->nama_sie ?? '-' }}</div>
                        </div>

                        <div class="rounded-lg border bg-white p-4">
                            <div class="text-gray-500">Pengaju</div>
                            <div class="text-gray-900 font-semibold mt-1">{{ $proposal->pengaju?->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $proposal->pengaju?->email ?? '-' }}</div>
                        </div>

                        <div class="rounded-lg border bg-white p-4">
                            <div class="text-gray-500">Lampiran</div>
                            <div class="text-gray-900 font-semibold mt-1">{{ $proposal->files?->count() ?? 0 }} file</div>
                        </div>
                    </div>

                    <div class="rounded-lg border bg-white p-4">
                        <div class="text-sm text-gray-500">Tujuan / Keterangan</div>
                        <div class="mt-2 text-gray-900 whitespace-pre-line leading-relaxed">
                            {{ $proposal->tujuan }}
                        </div>
                    </div>

                    @if(!empty($proposal->notes))
                        <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                            <div class="text-sm font-semibold text-yellow-800">Notes</div>
                            <div class="text-sm text-gray-900 whitespace-pre-line mt-2">{{ $proposal->notes }}</div>
                        </div>
                    @endif

                    @if($proposal->status === 'revisi' && !empty($proposal->reject_reason))
                        <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                            <div class="text-sm font-semibold text-red-700">Catatan Revisi</div>
                            <div class="text-sm text-gray-900 whitespace-pre-line mt-2">{{ $proposal->reject_reason }}</div>
                        </div>
                    @endif

                    {{-- Lampiran --}}
                    <div class="rounded-lg border bg-white p-4 space-y-3">
                        <div class="text-sm font-semibold text-gray-900">Lampiran Proposal</div>

                        @if(($proposal->files?->count() ?? 0) === 0)
                            <div class="text-sm text-gray-600">Belum ada file.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wide">
                                            <th class="px-4 py-3">Nama File</th>
                                            <th class="px-4 py-3">Tipe</th>
                                            <th class="px-4 py-3">Ukuran</th>
                                            <th class="px-4 py-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach($proposal->files as $f)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3">
                                                    <div class="text-sm text-gray-900 font-medium">{{ $f->original_name }}</div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-700">{{ $f->mime_type ?? 'application/pdf' }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-700">
                                                    @php $kb = $f->file_size ? number_format($f->file_size / 1024, 2) : null; @endphp
                                                    {{ $kb ? $kb.' KB' : '-' }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center gap-2 flex-wrap">
                                                        <a href="{{ route('proposals.files.preview', [$proposal, $f]) }}" target="_blank"
                                                           class="px-3 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                                            Preview
                                                        </a>

                                                        <a href="{{ route('proposals.files.download', [$proposal, $f]) }}"
                                                           class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                            Download
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Receipt --}}
                    @if($proposal->status === 'approved')
                        <div class="rounded-lg bg-green-50 border border-green-200 p-4">
                            <div class="text-sm font-semibold text-green-800">Bukti Penerimaan Proposal</div>
                            <div class="text-sm text-gray-700 mt-1">
                                Bukti penerimaan dapat diunduh/preview untuk kemudian di-print dan diserahkan ke Bendahara.
                            </div>

                            <div class="mt-3 flex gap-2 flex-wrap">
                                <a href="{{ route('proposals.receipt.preview', $proposal) }}" target="_blank"
                                   class="px-3 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900 text-sm">
                                    Preview Bukti
                                </a>
                                <a href="{{ route('proposals.receipt.download', $proposal) }}"
                                   class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                    Download Bukti
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Aksi --}}
                    <div class="rounded-lg border bg-white p-4 space-y-3">
                        <div class="text-sm font-semibold text-gray-900">Aksi</div>

                        @can('approveKetuaBidang', $proposal)
                            @if($proposal->status === 'menunggu_ketua_bidang' && $proposal->stage === 'ketua_bidang')
                                <form method="POST" action="{{ route('proposals.approve_ketua_bidang', $proposal) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium">
                                        Approve (Ketua Bidang)
                                    </button>
                                </form>
                            @endif
                        @endcan

                        @can('setDppDeadline', $proposal)
                            @if($proposal->stage === 'dpp_harian')
                                <div class="rounded-lg border bg-gray-50 p-4">
                                    <div class="text-sm font-semibold text-gray-900">Atur Durasi DPP Harian</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Default 3 hari. Durasi ini menentukan kapan proposal berpindah ke tahap Romo.
                                    </div>

                                    <form method="POST" action="{{ route('proposals.set_dpp_deadline', $proposal) }}"
                                          class="mt-3 flex flex-col sm:flex-row gap-2 sm:items-end">
                                        @csrf
                                        @method('PATCH')

                                        <div class="w-full sm:w-56">
                                            <label class="text-xs text-gray-600">Jumlah hari</label>
                                            <input type="number" name="dpp_days" min="1" max="30" value="3"
                                                   class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                                        </div>

                                        <button type="submit"
                                                class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
                                            Simpan Durasi
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endcan

                        @can('endDppHarian', $proposal)
                            @if($proposal->stage === 'dpp_harian' && $proposal->status === 'dpp_harian')
                                <form method="POST" action="{{ route('proposals.end_dpp_harian', $proposal) }}"
                                      class="end-dpp-form">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium">
                                        Kirim ke Romo Sekarang
                                    </button>

                                    <div class="text-xs text-gray-500 mt-2">
                                        Tombol ini mempercepat proses tanpa menunggu durasi DPP Harian habis.
                                    </div>
                                </form>
                            @endif
                        @endcan

                        @can('addNotes', $proposal)
                            @if(in_array($proposal->stage, ['dpp_harian','romo'], true))
                                <div class="rounded-lg border bg-gray-50 p-4">
                                    <div class="text-sm font-semibold text-gray-900">Tambahkan Notes</div>

                                    <form method="POST" action="{{ route('proposals.add_notes', $proposal) }}" class="mt-3 space-y-2">
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

                        {{-- ✅ Tombol Hapus akan muncul untuk:
                             - Romo / Sekretaris 1/2 kapan saja
                             - Ketua Sie jika status = revisi --}}
                        @can('delete', $proposal)
                            <form method="POST" action="{{ route('proposals.destroy', $proposal) }}"
                                  class="proposal-delete-form"
                                  data-title="{{ $proposal->judul }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium">
                                    Hapus Proposal
                                </button>
                            </form>
                        @endcan

                        @can('approveRomo', $proposal)
                            @if($proposal->status === 'menunggu_romo' && $proposal->stage === 'romo')
                                <div class="flex gap-2 flex-wrap">
                                    <form method="POST" action="{{ route('proposals.approve_romo', $proposal) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium">
                                            Approve (Romo)
                                        </button>
                                    </form>

                                    <details class="w-full sm:w-auto">
                                        <summary class="list-none cursor-pointer px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium inline-flex items-center gap-2">
                                            Revisi (Romo)
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M6 9l6 6 6-6"/>
                                            </svg>
                                        </summary>

                                        <div class="mt-2 rounded-lg border bg-white p-4">
                                            <form method="POST" action="{{ route('proposals.reject_romo', $proposal) }}" class="space-y-2">
                                                @csrf
                                                @method('PATCH')

                                                <label class="text-xs text-gray-600">Catatan Revisi</label>
                                                <textarea name="reject_reason" rows="4"
                                                          class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                                          placeholder="Tulis catatan revisi yang jelas..." required>{{ old('reject_reason') }}</textarea>

                                                <button type="submit"
                                                        class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium">
                                                    Kirim Revisi
                                                </button>
                                            </form>
                                        </div>
                                    </details>
                                </div>
                            @endif
                        @endcan
                    </div>

                </div>
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

        document.querySelectorAll('.proposal-delete-form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const title = form.getAttribute('data-title') || 'proposal ini';

                const res = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus Proposal?',
                    text: `Yakin ingin menghapus "${title}"? Semua lampiran & bukti penerimaan ikut terhapus.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                });

                if (res.isConfirmed) form.submit();
            });
        });

        document.querySelectorAll('.end-dpp-form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const res = await Swal.fire({
                    icon: 'question',
                    title: 'Kirim ke Romo sekarang?',
                    text: 'Ini akan mengakhiri masa DPP Harian dan memindahkan proposal ke tahap Romo.',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, kirim',
                    cancelButtonText: 'Batal'
                });

                if (res.isConfirmed) form.submit();
            });
        });
    </script>
</x-app-layout>
