<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Riwayat Pengajuan Pengesahan</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto">

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="card p-4 shadow-sm">

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama Dokumen</th>
                        <th>Tujuan</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($docs as $d)
                        <tr>
                            <td>{{ $d->original_name }}</td>
                            <td>{{ $d->tujuan }}</td>
                            <td>
                                @if($d->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($d->status === 'accepted')
                                    <span class="badge bg-success">Diterima</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>{{ $d->created_at->format('d-m-Y H:i') }}</td>

                            <td class="text-end">
                                <a href="{{ route('pengesahan.preview', $d) }}"
                                   target="_blank" class="btn btn-sm btn-primary">
                                    Lihat
                                </a>

                                <form method="POST"
                                      action="{{ route('pengesahan.userDestroy', $d) }}"
                                      class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger">
                                        Hapus
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                Belum ada pengajuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

    {{-- SWEETALERT DELETE --}}
    <script>
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e){
                e.preventDefault();
                Swal.fire({
                    title: "Hapus Pengajuan?",
                    text: "Tindakan ini tidak dapat dibatalkan.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Hapus"
                }).then(result => {
                    if(result.isConfirmed) form.submit();
                });
            });
        });
    </script>

</x-app-layout>
