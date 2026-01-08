<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Buat Surat Pengesahan — {{ $doc->original_name }}</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="card p-4 shadow-sm">
            <form method="POST" action="{{ route('pengesahan.suratStore', $doc) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama Pengesah</label>
                    <input type="text" 
                           name="nama_pengesah" 
                           class="form-control"
                           required>
                    <div class="form-text">
                        Contoh: Pdt. Yohanes, Ketua Umum Gereja.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tujuan Pengesahan</label>
                    <input type="text" 
                           name="tujuan" 
                           value="{{ $doc->tujuan }}"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Disahkan</label>
                    <input type="text" 
                           class="form-control"
                           value="{{ now()->format('d M Y') }}"
                           disabled>
                </div>

                <hr>

                <button class="btn btn-primary px-4">Buat Surat Pengesahan</button>

                @if ($doc->surat_path)
                    <a href="{{ Storage::url($doc->surat_path) }}" 
                       target="_blank" 
                       class="btn btn-success px-4 ms-2">
                        Lihat Surat
                    </a>
                @endif
            </form>
        </div>

    </div>
</x-app-layout>
