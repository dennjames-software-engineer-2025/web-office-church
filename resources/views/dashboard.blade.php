<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
            <div class="text-xs text-gray-500">
                {{ now()->format('d M Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- HERO / WELCOME --}}
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">
                            Selamat datang, {{ auth()->user()->name }} 👋
                        </h3>

                        <p class="text-sm text-gray-600 mt-1">
                            Kedudukan:
                            <span class="font-semibold">
                                {{ strtoupper(str_replace('_',' ', auth()->user()->kedudukan ?? '-')) }}
                            </span>
                            —
                            Role:
                            <span class="font-semibold">
                                {{ auth()->user()->getRoleNames()->implode(', ') ?: '-' }}
                            </span>
                        </p>

                        <p class="text-xs text-gray-500 mt-2">
                            Silakan cek informasi terbaru gereja di bawah ini.
                        </p>
                    </div>

                    {{-- Badge kecil --}}
                    <div class="flex gap-2">
                        <span class="px-3 py-1 rounded-full text-xs bg-blue-50 text-blue-700 border border-blue-100">
                            Info Gereja
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs bg-gray-50 text-gray-700 border border-gray-100">
                            Update Internal
                        </span>
                    </div>
                </div>
            </div>

            {{-- SECTION: ACARA TERDEKAT --}}
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Acara Terdekat</h3>
                    <span class="text-xs text-gray-500">Minggu ini & minggu depan</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Card acara 1 --}}
                    <div class="border rounded-lg p-5 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs text-gray-500">Misa / Ibadah</div>
                                <div class="mt-1 font-semibold text-gray-900">Misa Minggu</div>
                            </div>
                            <span class="text-xs px-2 py-1 rounded bg-green-50 text-green-700 border border-green-100">
                                Terjadwal
                            </span>
                        </div>

                        <div class="mt-3 text-sm text-gray-600 space-y-1">
                            <div>🗓️ Minggu, 08.00 & 17.00</div>
                            <div>📍 Gereja Utama</div>
                            <div>👤 Petugas: Tim Liturgi</div>
                        </div>

                        <div class="mt-4 text-xs text-gray-500">
                            Catatan: Hadir 15 menit lebih awal.
                        </div>
                    </div>

                    {{-- Card acara 2 --}}
                    <div class="border rounded-lg p-5 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs text-gray-500">Pertemuan</div>
                                <div class="mt-1 font-semibold text-gray-900">Rapat Koordinasi Pelayanan</div>
                            </div>
                            <span class="text-xs px-2 py-1 rounded bg-yellow-50 text-yellow-700 border border-yellow-100">
                                Persiapan
                            </span>
                        </div>

                        <div class="mt-3 text-sm text-gray-600 space-y-1">
                            <div>🗓️ Rabu, 19.30</div>
                            <div>📍 Ruang Rapat</div>
                            <div>👤 Undangan: Inti & Koordinator</div>
                        </div>

                        <div class="mt-4 text-xs text-gray-500">
                            Mohon bawa catatan kegiatan minggu ini.
                        </div>
                    </div>

                    {{-- Card acara 3 --}}
                    <div class="border rounded-lg p-5 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs text-gray-500">Kegiatan</div>
                                <div class="mt-1 font-semibold text-gray-900">Aksi Sosial “Berbagi Kasih”</div>
                            </div>
                            <span class="text-xs px-2 py-1 rounded bg-blue-50 text-blue-700 border border-blue-100">
                                Pendaftaran
                            </span>
                        </div>

                        <div class="mt-3 text-sm text-gray-600 space-y-1">
                            <div>🗓️ Sabtu, 09.00</div>
                            <div>📍 Aula Paroki</div>
                            <div>👤 PIC: Bidang Formatio</div>
                        </div>

                        <div class="mt-4 text-xs text-gray-500">
                            Info lengkap ada di dokumen pengumuman (sekretaris).
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION: BERITA & INFORMASI --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Kolom 1-2: Berita --}}
                <div class="lg:col-span-2 bg-white shadow sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Berita & Informasi</h3>
                        <span class="text-xs text-gray-500">Terbaru</span>
                    </div>

                    <div class="space-y-3">
                        {{-- Item berita --}}
                        <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <div class="font-semibold text-gray-900">Perubahan Jadwal Kegiatan Mingguan</div>
                                <span class="text-xs text-gray-500">Hari ini</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                Ada penyesuaian jadwal untuk beberapa kegiatan. Silakan cek update terbaru dari sekretariat.
                            </p>
                        </div>

                        <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <div class="font-semibold text-gray-900">Pengumpulan Laporan Bulanan</div>
                                <span class="text-xs text-gray-500">Minggu ini</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                Mohon setiap bidang mengunggah laporan bulanan pada menu Dokumen sebelum akhir minggu.
                            </p>
                        </div>

                        <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <div class="font-semibold text-gray-900">Informasi Alur Program & Proposal</div>
                                <span class="text-xs text-gray-500">Update</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                Proposal wajib mengikuti alur persetujuan. Pastikan file final sudah benar sebelum submit.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Kolom 3: Catatan / Pengingat --}}
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengingat</h3>

                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex gap-2">
                            <span>✅</span>
                            <div>
                                <div class="font-semibold">Upload Laporan</div>
                                <div class="text-gray-600 text-xs">Gunakan menu Dokumen untuk unggah laporan resmi.</div>
                            </div>
                        </li>

                        <li class="flex gap-2">
                            <span>✅</span>
                            <div>
                                <div class="font-semibold">Dokumen Dibagikan</div>
                                <div class="text-gray-600 text-xs">Cek menu Dokumen Dibagikan untuk file dari sekretaris.</div>
                            </div>
                        </li>

                        <li class="flex gap-2">
                            <span>✅</span>
                            <div>
                                <div class="font-semibold">Kerapihan File</div>
                                <div class="text-gray-600 text-xs">Pastikan nama file jelas (tanggal + judul).</div>
                            </div>
                        </li>
                    </ul>

                    <div class="mt-5 p-4 rounded-lg bg-blue-50 border border-blue-100">
                        <div class="text-sm font-semibold text-blue-900">Butuh bantuan?</div>
                        <div class="text-xs text-blue-700 mt-1">
                            Hubungi sekretariat atau super admin untuk kendala akses.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
