<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Buat Akun User
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 sm:p-8 shadow rounded-lg">

                <p class="text-sm text-gray-600 mb-6">
                    Form ini hanya dapat digunakan oleh <strong>Super Admin</strong>.
                    Akun yang dibuat akan langsung berstatus
                    <span class="font-semibold text-green-700">Active</span>.
                </p>

                {{-- GLOBAL ERROR --}}
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        <p class="font-semibold mb-2">Gagal menyimpan. Periksa kembali input berikut:</p>
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- FORM --}}
                <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- NAMA + EMAIL --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" value="Nama Lengkap" />
                            <x-text-input
                                id="name"
                                type="text"
                                name="name"
                                class="mt-1 block w-full"
                                placeholder="Contoh: Dennis Michael"
                                value="{{ old('name') }}"
                                required
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input
                                id="email"
                                type="email"
                                name="email"
                                class="mt-1 block w-full"
                                placeholder="Contoh: user@gmail.com"
                                value="{{ old('email') }}"
                                required
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    {{-- KEDUDUKAN --}}
                    <div>
                        <x-input-label for="kedudukan" value="Kedudukan" />
                        <select
                            id="kedudukan"
                            name="kedudukan"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        >
                            <option value="">-- Pilih Kedudukan --</option>
                            <option value="dpp_inti" {{ old('kedudukan')==='dpp_inti' ? 'selected' : '' }}>DPP Inti</option>
                            <option value="bgkp" {{ old('kedudukan')==='bgkp' ? 'selected' : '' }}>BGKP</option>
                            <option value="lingkungan" {{ old('kedudukan')==='lingkungan' ? 'selected' : '' }}>Lingkungan</option>
                            <option value="sekretariat" {{ old('kedudukan')==='sekretariat' ? 'selected' : '' }}>Sekretariat</option>
                        </select>
                        <x-input-error :messages="$errors->get('kedudukan')" class="mt-2" />
                    </div>

                    {{-- JABATAN / ROLE --}}
                    <div>
                        <x-input-label for="jabatan" value="Jabatan / Role" />
                        <select
                            id="jabatan"
                            name="jabatan"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        >
                            <option value="">-- Pilih Jabatan --</option>

                            {{-- DPP Inti + BGKP --}}
                            <option value="ketua" data-kedudukan="dpp_inti,bgkp" {{ old('jabatan')==='ketua'?'selected':'' }}>Ketua</option>
                            <option value="wakil_ketua" data-kedudukan="dpp_inti,bgkp" {{ old('jabatan')==='wakil_ketua'?'selected':'' }}>Wakil Ketua</option>
                            <option value="sekretaris_1" data-kedudukan="dpp_inti,bgkp" {{ old('jabatan')==='sekretaris_1'?'selected':'' }}>Sekretaris 1</option>
                            <option value="sekretaris_2" data-kedudukan="dpp_inti,bgkp" {{ old('jabatan')==='sekretaris_2'?'selected':'' }}>Sekretaris 2</option>
                            <option value="bendahara_1" data-kedudukan="dpp_inti,bgkp" {{ old('jabatan')==='bendahara_1'?'selected':'' }}>Bendahara 1</option>
                            <option value="bendahara_2" data-kedudukan="dpp_inti,bgkp" {{ old('jabatan')==='bendahara_2'?'selected':'' }}>Bendahara 2</option>

                            {{-- KHUSUS DPP Inti --}}
                            <option value="ketua_bidang" data-kedudukan="dpp_inti" {{ old('jabatan')==='ketua_bidang'?'selected':'' }}>Ketua Bidang</option>
                            <option value="ketua_sie" data-kedudukan="dpp_inti" {{ old('jabatan')==='ketua_sie'?'selected':'' }}>Ketua Sie</option>

                            {{-- Lingkungan --}}
                            <option value="ketua_lingkungan" data-kedudukan="lingkungan" {{ old('jabatan')==='ketua_lingkungan'?'selected':'' }}>Ketua Lingkungan</option>
                            <option value="wakil_ketua_lingkungan" data-kedudukan="lingkungan" {{ old('jabatan')==='wakil_ketua_lingkungan'?'selected':'' }}>Wakil Ketua Lingkungan</option>
                            <option value="anggota_komunitas" data-kedudukan="lingkungan" {{ old('jabatan')==='anggota_komunitas'?'selected':'' }}>Anggota Komunitas</option>

                            {{-- Sekretariat --}}
                            <option value="sekretariat" data-kedudukan="sekretariat" {{ old('jabatan')==='sekretariat'?'selected':'' }}>Sekretariat</option>
                        </select>

                        <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
                    </div>

                    {{-- BIDANG --}}
                    {{-- BIDANG --}}
                    <div id="wrapBidang" class="hidden">
                        <x-input-label for="bidang_id" value="Bidang" />
                        <select
                            id="bidang_id"
                            name="bidang_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">-- Pilih Bidang --</option>
                            @foreach($bidangs as $b)
                                <option value="{{ $b->id }}" {{ (string)old('bidang_id')===(string)$b->id ? 'selected':'' }}>
                                    {{ $b->nama_bidang }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('bidang_id')" class="mt-2" />
                    </div>

                    {{-- SIE --}}
                    <div id="wrapSie" class="hidden">
                        <x-input-label for="sie_id" value="Sie" />
                        <select
                            id="sie_id"
                            name="sie_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">-- Pilih Sie --</option>
                            @foreach($sies as $s)
                                <option value="{{ $s->id }}"
                                    data-bidang="{{ $s->bidang_id }}"
                                    {{ (string)old('sie_id')===(string)$s->id ? 'selected':'' }}>
                                    {{ $s->nama_sie }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('sie_id')" class="mt-2" />
                    </div>
                    {{-- END --}}

                    {{-- PASSWORD --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="password" value="Password" />
                            <x-text-input
                                id="password"
                                name="password"
                                type="password"
                                class="mt-1 block w-full"
                                placeholder="Minimal 8 karakter"
                                required
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                            <x-text-input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                required
                            />
                        </div>
                    </div>

                    {{-- BUTTONS --}}
                    <div class="flex items-center justify-end gap-3 pt-4">
                        <a href="{{ route('users.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                            Batal
                        </a>

                        <x-primary-button>
                            Simpan Akun User
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const kedudukan = document.getElementById('kedudukan');
        const jabatan   = document.getElementById('jabatan');

        const wrapBidang = document.getElementById('wrapBidang');
        const wrapSie    = document.getElementById('wrapSie');
        const bidang     = document.getElementById('bidang_id');
        const sie        = document.getElementById('sie_id');

        function filterJabatanByKedudukan() {
            const k = kedudukan.value;

            [...jabatan.options].forEach(opt => {
                if (!opt.value) return; // skip placeholder
                const allowed = (opt.dataset.kedudukan || '').split(',').map(s => s.trim());
                const show = allowed.includes(k);

                opt.hidden = !show;
                opt.disabled = !show;
            });

            // kalau jabatan yg kepilih jadi tidak valid, reset
            const selected = jabatan.options[jabatan.selectedIndex];
            if (selected && selected.disabled) {
                jabatan.value = '';
            }
        }

        function toggleBidangSie() {
            const j = jabatan.value;

            const needsBidang = (j === 'ketua_bidang' || j === 'ketua_sie');
            const needsSie    = (j === 'ketua_sie');

            wrapBidang.classList.toggle('hidden', !needsBidang);
            wrapSie.classList.toggle('hidden', !needsSie);

            if (!needsBidang) {
                bidang.value = '';
            }
            if (!needsSie) {
                sie.value = '';
            }

            filterSieByBidang(); // biar sync
        }

        function filterSieByBidang() {
            const bidangId = bidang.value;

            [...sie.options].forEach(opt => {
                if (!opt.value) return; // placeholder
                const match = opt.dataset.bidang === bidangId;
                opt.hidden = !match;
                opt.disabled = !match;
            });

            const selected = sie.options[sie.selectedIndex];
            if (selected && selected.disabled) {
                sie.value = '';
            }
        }

        kedudukan.addEventListener('change', () => {
            filterJabatanByKedudukan();
            toggleBidangSie();
        });

        jabatan.addEventListener('change', toggleBidangSie);
        bidang.addEventListener('change', filterSieByBidang);

        // initial load (old() case)
        filterJabatanByKedudukan();
        toggleBidangSie();
    });
    </script>

</x-app-layout>
