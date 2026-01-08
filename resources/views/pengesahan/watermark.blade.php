<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Beri Watermark — {{ $doc->original_name }}</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card p-4 shadow-sm mb-4">
            <h5 class="mb-3">Pratinjau Dokumen</h5>

            <iframe 
                src="{{ route('pengesahan.preview', $doc) }}"
                style="width:100%; height:600px; border:1px solid #ddd;">
            </iframe>
        </div>

        <div class="card p-4 shadow-sm">
            <h5>Proses Watermark</h5>
            <p class="text-muted mb-3">
                Tekan tombol di bawah ini untuk memberikan watermark “DISAHKAN” pada setiap halaman PDF.
            </p>

            <form method="GET" action="{{ route('pengesahan.watermarkStore', $doc) }}">
                <button class="btn btn-primary px-4">
                    Buat Watermark
                </button>
            </form>

            @if ($doc->watermark_path)
                <hr class="my-4">
                <h6>Watermark Sudah Dibuat</h6>
                <a href="{{ Storage::url($doc->watermark_path) }}" 
                   target="_blank" class="btn btn-success mt-2">
                    Lihat Hasil Watermark
                </a>
            @endif
        </div>

    </div>
</x-app-layout>
