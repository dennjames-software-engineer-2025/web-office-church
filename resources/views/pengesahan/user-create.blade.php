<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Upload Dokumen untuk Pengesahan</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">
        
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        
        <div class="card p-4 shadow-sm">
            <form method="POST" enctype="multipart/form-data"
                  action="{{ route('pengesahan.userStore') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Tujuan Pengesahan</label>
                    <input type="text" name="tujuan"
                           value="{{ old('tujuan') }}"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload PDF</label>
                    <input type="file" name="file"
                           class="form-control" accept="application/pdf" required>
                </div>

                <button class="btn btn-primary">Kirim Pengajuan</button>
            </form>
        </div>

    </div>
</x-app-layout>
