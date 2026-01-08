<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Digital Signature
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto bg-white shadow p-6 rounded">

            {{-- Status --}}
            @if(session('status'))
                <div class="mb-4 px-4 py-2 bg-green-100 text-green-700 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <h3 class="text-lg font-semibold mb-4">Gambar Tanda Tangan Anda</h3>

            {{-- Canvas --}}
            <div class="border rounded-lg p-4 bg-gray-50">
                <canvas id="signaturePad" class="border w-full" height="250"></canvas>
            </div>

            <div class="flex gap-4 mt-4">
                <button id="clearBtn"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Bersihkan
                </button>

                <button id="saveBtn"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Simpan Tanda Tangan
                </button>
            </div>

            {{-- Form POST --}}
            <form id="saveForm" method="POST" action="{{ route('tools.signature.save') }}" class="hidden">
                @csrf
                <input type="hidden" name="signature" id="signatureInput">
            </form>

        </div>
    </div>

    {{-- Signature Pad JS --}}
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>

    <script>
        const canvas = document.getElementById('signaturePad');

        // Sesuaikan ukuran pixel density supaya smooth
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
        }

        window.onresize = resizeCanvas;
        resizeCanvas();

        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255,255,255,0)',
            penColor: 'black',
        });

        document.getElementById('clearBtn').onclick = function () {
            signaturePad.clear();
        };

        document.getElementById('saveBtn').onclick = function () {
            if (signaturePad.isEmpty()) {
                alert('Silakan gambar tanda tangan terlebih dahulu.');
                return;
            }

            // Ambil PNG base64
            const data = signaturePad.toDataURL('image/png');

            // Masukkan ke hidden input
            document.getElementById('signatureInput').value = data;

            // Submit form
            document.getElementById('saveForm').submit();
        };
    </script>

</x-app-layout>