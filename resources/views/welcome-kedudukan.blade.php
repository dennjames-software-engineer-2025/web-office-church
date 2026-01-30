<x-guest-layout>
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-10">
            <h1 class="text-2xl font-bold text-gray-900">
                Selamat Datang di Website Manajemen Arsip Redemptor Mundi
            </h1>
            <p class="text-sm text-gray-600 mt-2">
                Silakan pilih kedudukan untuk masuk.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <a href="{{ route('login.kedudukan', ['kedudukan' => 'dpp_inti']) }}"
               class="block rounded-xl border bg-white p-6 shadow hover:shadow-md transition">
                <div class="text-lg font-semibold text-gray-900">DPP Harian</div>
                <div class="text-sm text-gray-600 mt-1">DPP Inti, Ketua Bidang + Sie terkait</div>
            </a>

            <a href="{{ route('login.kedudukan', ['kedudukan' => 'bgkp']) }}"
               class="block rounded-xl border bg-white p-6 shadow hover:shadow-md transition">
                <div class="text-lg font-semibold text-gray-900">BGKP</div>
                <div class="text-sm text-gray-600 mt-1">Pengurus BGKP</div>
            </a>

            <a href="{{ route('login.kedudukan', ['kedudukan' => 'lingkungan']) }}"
               class="block rounded-xl border bg-white p-6 shadow hover:shadow-md transition">
                <div class="text-lg font-semibold text-gray-900">Lingkungan</div>
                <div class="text-sm text-gray-600 mt-1">Ketua Lingkungan & Panitia Acara</div>
            </a>

            <a href="{{ route('login.kedudukan', ['kedudukan' => 'sekretariat']) }}"
               class="block rounded-xl border bg-white p-6 shadow hover:shadow-md transition">
                <div class="text-lg font-semibold text-gray-900">Sekretariat</div>
                <div class="text-sm text-gray-600 mt-1">Staf Sekretariat</div>
            </a>
        </div>
    </div>
</x-guest-layout>
