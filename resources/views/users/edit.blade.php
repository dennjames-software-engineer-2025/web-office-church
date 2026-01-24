<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit User
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6 sm:p-8">

                <p class="text-sm text-gray-600 mb-6">
                    Perbarui data user dan pastikan status, kedudukan, jabatan, serta relasi Bidang/Sie/Lingkungan sudah benar.
                </p>

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

                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Nama + Email --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" value="Nama Lengkap" />
                            <x-text-input
                                id="name"
                                type="text"
                                name="name"
                                class="mt-1 block w-full"
                                value="{{ old('name', $user->name) }}"
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
                                value="{{ old('email', $user->email) }}"
                                required
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select
                            id="status"
                            name="status"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        >
                            @php
                                $statusVal = old('status', $user->status);
                            @endphp
                            @foreach(['pending','active','rejected','suspended'] as $st)
                                <option value="{{ $st }}" {{ $statusVal === $st ? 'selected' : '' }}>
                                    {{ ucfirst($st) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    {{-- Kedudukan --}}
                    <div>
                        <x-input-label for="kedudukan" value="Kedudukan" />
                        <select
                            id="kedudukan"
                            name="kedudukan"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        >
                            <option value="">-- Pilih Kedudukan --</option>
                            <option value="dpp_inti" {{ old('kedudukan', $user->kedudukan)==='dpp_inti' ? 'selected' : '' }}>DPP Inti</option>
                            <option value="bgkp" {{ old('kedudukan', $user->kedudukan)==='bgkp' ? 'selected' : '' }}>BGKP</option>
                            <option value="lingkungan" {{ old('kedudukan', $user->kedudukan)==='lingkungan' ? 'selected' : '' }}>Lingkungan</option>
                            <option value="sekretariat" {{ old('kedudukan', $user->kedudukan)==='sekretariat' ? 'selected' : '' }}>Sekretariat</option>
                        </select>
                        <x-input-error :messages="$errors->get('kedudukan')" class="mt-2" />
                    </div>

                    {{-- KHUSUS LINGKUNGAN: SCOPE --}}
                    <div id="wrapLingScope" class="hidden">
                        <x-input-label for="lingkungan_scope" value="Scope" />
                        <select
                            id="lingkungan_scope"
                            name="lingkungan_scope"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">-- Pilih Scope --</option>
                            <option value="wilayah"
                                {{ old('lingkungan_scope', $user->lingkungan_scope)==='wilayah' ? 'selected' : '' }}>
                                Wilayah
                            </option>
                            <option value="lingkungan"
                                {{ old('lingkungan_scope', $user->lingkungan_scope)==='lingkungan' ? 'selected' : '' }}>
                                Lingkungan
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('lingkungan_scope')" class="mt-2" />
                    </div>

                    {{-- KHUSUS LINGKUNGAN: WILAYAH --}}
                    <div id="wrapWilayah" class="hidden">
                        <x-input-label for="wilayah" value="Wilayah" />
                        <select
                            id="wilayah"
                            name="wilayah"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">-- Pilih Wilayah --</option>
                            @foreach(array_keys($lingkunganMap ?? []) as $w)
                                <option value="{{ $w }}" {{ old('wilayah', $user->wilayah)===$w ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', strtoupper($w)) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('wilayah')" class="mt-2" />
                    </div>

                    {{-- KHUSUS LINGKUNGAN: LINGKUNGAN (muncul hanya jika scope=lingkungan) --}}
                    <div id="wrapLingkungan" class="hidden">
                        <x-input-label for="lingkungan" value="Lingkungan" />
                        <select
                            id="lingkungan"
                            name="lingkungan"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">-- Pilih Lingkungan --</option>
                        </select>
                        <x-input-error :messages="$errors->get('lingkungan')" class="mt-2" />
                    </div>

                    {{-- Jabatan --}}
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
                            <option value="ketua" data-kedudukan="dpp_inti,bgkp" {{ old('jabatan', $user->jabatan)==='ketua'?'selected':'' }}>Ketua</option>
                            <option value="wakil_ketua" data-kedudukan="dpp_inti,bgkp" {{ old('jabatan', $user->jabatan)==='wakil_ketua'?'selected':'' }}>Wakil Ketua</option>
                            <option value="sekretaris_1" data-kedudukan="dpp_inti,bgkp" {{ old('jabatan', $user->jabatan)==='sekretaris_1'?'selected':'' }}>Sekretaris 1</option>
                            <option value="sekretaris_2" data-kedudukan="dpp_inti,bgkp" {{ old('jabatan', $user->jabatan)==='sekretaris_2'?'selected':'' }}>Sekretaris 2</option>
                            <option value="bendahara_1" data-kedudukan="dpp_inti,bgkp" {{ old('jabatan', $user->jabatan)==='bendahara_1'?'selected':'' }}>Bendahara 1</option>
                            <option value="bendahara_2" data-kedudukan="dpp_inti,bgkp" {{ old('jabatan', $user->jabatan)==='bendahara_2'?'selected':'' }}>Bendahara 2</option>

                            {{-- Ketua Bidang --}}
                            <option value="ketua_bidang" data-kedudukan="dpp_inti,bgkp" {{ old('jabatan', $user->jabatan)==='ketua_bidang'?'selected':'' }}>
                                Ketua Bidang
                            </option>

                            {{-- Ketua Sie: khusus DPP Inti --}}
                            <option value="ketua_sie" data-kedudukan="dpp_inti" {{ old('jabatan', $user->jabatan)==='ketua_sie'?'selected':'' }}>Ketua Sie</option>

                            {{-- Sekretariat --}}
                            <option value="sekretariat" data-kedudukan="sekretariat" {{ old('jabatan', $user->jabatan)==='sekretariat'?'selected':'' }}>Sekretariat</option>

                            {{-- ========================= --}}
                            {{-- ✅ LINGKUNGAN (BRIEF BARU) --}}
                            {{-- ========================= --}}

                            {{-- scope: wilayah --}}
                            <option value="fasilitator_wilayah" data-kedudukan="lingkungan" data-scope="wilayah" {{ old('jabatan', $user->jabatan)==='fasilitator_wilayah'?'selected':'' }}>Fasilitator Wilayah</option>
                            <option value="sekretaris_wilayah" data-kedudukan="lingkungan" data-scope="wilayah" {{ old('jabatan', $user->jabatan)==='sekretaris_wilayah'?'selected':'' }}>Sekretaris Wilayah</option>
                            <option value="bendahara_wilayah" data-kedudukan="lingkungan" data-scope="wilayah" {{ old('jabatan', $user->jabatan)==='bendahara_wilayah'?'selected':'' }}>Bendahara Wilayah</option>
                            <option value="sie_biak_wilayah" data-kedudukan="lingkungan" data-scope="wilayah" {{ old('jabatan', $user->jabatan)==='sie_biak_wilayah'?'selected':'' }}>Sie BIAK (Wilayah)</option>
                            <option value="sie_omk_wilayah" data-kedudukan="lingkungan" data-scope="wilayah" {{ old('jabatan', $user->jabatan)==='sie_omk_wilayah'?'selected':'' }}>Sie OMK (Wilayah)</option>
                            <option value="sie_keluarga_wilayah" data-kedudukan="lingkungan" data-scope="wilayah" {{ old('jabatan', $user->jabatan)==='sie_keluarga_wilayah'?'selected':'' }}>Sie Keluarga (Wilayah)</option>
                            <option value="sie_lansia_wilayah" data-kedudukan="lingkungan" data-scope="wilayah" {{ old('jabatan', $user->jabatan)==='sie_lansia_wilayah'?'selected':'' }}>Sie Lansia (Wilayah)</option>
                            <option value="sie_tatib_kolektan_wilayah" data-kedudukan="lingkungan" data-scope="wilayah" {{ old('jabatan', $user->jabatan)==='sie_tatib_kolektan_wilayah'?'selected':'' }}>Sie Tatib Kolektan (Wilayah)</option>

                            {{-- scope: lingkungan --}}
                            <option value="ketua_lingkungan" data-kedudukan="lingkungan" data-scope="lingkungan" {{ old('jabatan', $user->jabatan)==='ketua_lingkungan'?'selected':'' }}>Ketua Lingkungan</option>
                            <option value="wakil_ketua_lingkungan" data-kedudukan="lingkungan" data-scope="lingkungan" {{ old('jabatan', $user->jabatan)==='wakil_ketua_lingkungan'?'selected':'' }}>Wakil Ketua Lingkungan</option>
                            <option value="sekretaris_lingkungan" data-kedudukan="lingkungan" data-scope="lingkungan" {{ old('jabatan', $user->jabatan)==='sekretaris_lingkungan'?'selected':'' }}>Sekretaris Lingkungan</option>
                            <option value="bendahara_lingkungan" data-kedudukan="lingkungan" data-scope="lingkungan" {{ old('jabatan', $user->jabatan)==='bendahara_lingkungan'?'selected':'' }}>Bendahara Lingkungan</option>
                            <option value="seksi_liturgi" data-kedudukan="lingkungan" data-scope="lingkungan" {{ old('jabatan', $user->jabatan)==='seksi_liturgi'?'selected':'' }}>Seksi Liturgi</option>
                            <option value="seksi_katekese" data-kedudukan="lingkungan" data-scope="lingkungan" {{ old('jabatan', $user->jabatan)==='seksi_katekese'?'selected':'' }}>Seksi Katekese</option>
                            <option value="seksi_kerasulan_kitab_suci" data-kedudukan="lingkungan" data-scope="lingkungan" {{ old('jabatan', $user->jabatan)==='seksi_kerasulan_kitab_suci'?'selected':'' }}>Seksi Kerasulan Kitab Suci</option>
                            <option value="seksi_sosial" data-kedudukan="lingkungan" data-scope="lingkungan" {{ old('jabatan', $user->jabatan)==='seksi_sosial'?'selected':'' }}>Seksi Sosial</option>
                            <option value="seksi_pengabdian_masyarakat" data-kedudukan="lingkungan" data-scope="lingkungan" {{ old('jabatan', $user->jabatan)==='seksi_pengabdian_masyarakat'?'selected':'' }}>Seksi Pengabdian Masyarakat</option>
                            <option value="pelayanan_kematian" data-kedudukan="lingkungan" data-scope="lingkungan" {{ old('jabatan', $user->jabatan)==='pelayanan_kematian'?'selected':'' }}>Pelayanan Urusan Kematian</option>
                        </select>

                        <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
                    </div>

                    {{-- Bidang --}}
                    <div id="wrapBidang" class="hidden">
                        <x-input-label for="bidang_id" value="Bidang" />
                        <select
                            id="bidang_id"
                            name="bidang_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">-- Pilih Bidang --</option>
                            @foreach($bidangs as $b)
                                <option
                                    value="{{ $b->id }}"
                                    data-kedudukan="{{ $b->kedudukan }}"
                                    {{ (string)old('bidang_id', $user->bidang_id)===(string)$b->id ? 'selected':'' }}
                                >
                                    {{ $b->nama_bidang }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('bidang_id')" class="mt-2" />
                    </div>

                    {{-- Sie --}}
                    <div id="wrapSie" class="hidden">
                        <x-input-label for="sie_id" value="Sie" />
                        <select
                            id="sie_id"
                            name="sie_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">-- Pilih Sie --</option>
                            @foreach($sies as $s)
                                <option
                                    value="{{ $s->id }}"
                                    data-bidang="{{ $s->bidang_id }}"
                                    {{ (string)old('sie_id', $user->sie_id)===(string)$s->id ? 'selected':'' }}
                                >
                                    {{ $s->nama_sie }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('sie_id')" class="mt-2" />
                    </div>

                    {{-- Password opsional --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="password" value="Password Baru (Opsional)" />
                            <x-text-input
                                id="password"
                                name="password"
                                type="password"
                                class="mt-1 block w-full"
                                placeholder="Kosongkan jika tidak ingin ganti"
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
                            <x-text-input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                            />
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center justify-end gap-3 pt-4">
                        <a href="{{ route('users.show', $user) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                            Batal
                        </a>

                        <x-primary-button>
                            Simpan
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const lingkunganMap = @json($lingkunganMap ?? []);

        const kedudukan = document.getElementById('kedudukan');
        const jabatan   = document.getElementById('jabatan');

        const wrapBidang = document.getElementById('wrapBidang');
        const wrapSie    = document.getElementById('wrapSie');
        const bidang     = document.getElementById('bidang_id');
        const sie        = document.getElementById('sie_id');

        const wrapLingScope = document.getElementById('wrapLingScope');
        const wrapWilayah   = document.getElementById('wrapWilayah');
        const wrapLingkungan= document.getElementById('wrapLingkungan');

        const lingScope = document.getElementById('lingkungan_scope');
        const wilayah   = document.getElementById('wilayah');
        const lingkungan= document.getElementById('lingkungan');

        function isLingkunganSelected() {
            return kedudukan.value === 'lingkungan';
        }

        function filterJabatan() {
            const k  = kedudukan.value;
            const sc = lingScope.value;

            [...jabatan.options].forEach(opt => {
                if (!opt.value) return;

                const allowedK = (opt.dataset.kedudukan || '').split(',').map(s => s.trim()).filter(Boolean);
                let show = allowedK.includes(k);

                if (show && k === 'lingkungan') {
                    const optScope = opt.dataset.scope || '';
                    show = optScope ? (optScope === sc) : false;
                }

                opt.hidden = !show;
                opt.disabled = !show;
            });

            const selected = jabatan.options[jabatan.selectedIndex];
            if (selected && selected.disabled) jabatan.value = '';
        }

        function filterBidangByKedudukan() {
            const k = kedudukan.value;

            [...bidang.options].forEach(opt => {
                if (!opt.value) return;
                const show = opt.dataset.kedudukan === k;
                opt.hidden = !show;
                opt.disabled = !show;
            });

            const selected = bidang.options[bidang.selectedIndex];
            if (selected && selected.disabled) bidang.value = '';
        }

        function filterSieByBidang() {
            const bidangId = bidang.value;

            [...sie.options].forEach(opt => {
                if (!opt.value) return;
                const match = opt.dataset.bidang === bidangId;
                opt.hidden = !match;
                opt.disabled = !match;
            });

            const selected = sie.options[sie.selectedIndex];
            if (selected && selected.disabled) sie.value = '';
        }

        function toggleBidangSie() {
            const j = jabatan.value;
            const needsBidang = (j === 'ketua_bidang' || j === 'ketua_sie');
            const needsSie    = (j === 'ketua_sie');

            wrapBidang.classList.toggle('hidden', !needsBidang);
            wrapSie.classList.toggle('hidden', !needsSie);

            if (needsBidang) {
                filterBidangByKedudukan();
            } else {
                bidang.value = '';
            }

            if (needsSie) {
                filterSieByBidang();
            } else {
                sie.value = '';
            }
        }

        function rebuildLingkunganOptions() {
            const w = wilayah.value;
            const list = lingkunganMap[w] || [];

            lingkungan.innerHTML = '<option value="">-- Pilih Lingkungan --</option>';

            list.forEach(name => {
                const opt = document.createElement('option');
                opt.value = name;
                opt.textContent = name;
                lingkungan.appendChild(opt);
            });

            // re-apply old() / current user
            const oldVal = @json(old('lingkungan', $user->lingkungan));
            if (oldVal) {
                lingkungan.value = oldVal;
            }
        }

        function toggleLingkunganFields() {
            const isLing = isLingkunganSelected();

            wrapLingScope.classList.toggle('hidden', !isLing);
            wrapWilayah.classList.toggle('hidden', !isLing);

            const showLingkungan = isLing && (lingScope.value === 'lingkungan');
            wrapLingkungan.classList.toggle('hidden', !showLingkungan);

            if (!isLing) {
                lingScope.value = '';
                wilayah.value = '';
                lingkungan.innerHTML = '<option value="">-- Pilih Lingkungan --</option>';
            } else {
                if (wilayah.value) rebuildLingkunganOptions();
            }

            if (isLing) {
                wrapBidang.classList.add('hidden');
                wrapSie.classList.add('hidden');
                bidang.value = '';
                sie.value = '';
            }
        }

        kedudukan.addEventListener('change', () => {
            toggleLingkunganFields();
            filterJabatan();
            toggleBidangSie();
        });

        lingScope.addEventListener('change', () => {
            toggleLingkunganFields();
            filterJabatan();
            toggleBidangSie();
        });

        wilayah.addEventListener('change', () => {
            rebuildLingkunganOptions();
        });

        jabatan.addEventListener('change', () => {
            toggleBidangSie();
        });

        bidang.addEventListener('change', filterSieByBidang);

        // initial
        toggleLingkunganFields();
        filterJabatan();
        toggleBidangSie();

        if (kedudukan.value === 'lingkungan' && wilayah.value) {
            rebuildLingkunganOptions();
        }
    });
    </script>
</x-app-layout>
