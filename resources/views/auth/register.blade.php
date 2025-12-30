<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text"
                          name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email"
                          name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Tim --}}
        <div class="mt-4">
            <x-input-label for="team_type" value="Tim" />
            <select name="team_type" id="team_type" class="block mt-1 w-full text-gray-900 bg-white" required>
                <option value="">-- Pilih Tim --</option>
                <option value="inti"   {{ old('team_type') === 'inti'   ? 'selected' : '' }}>Tim Inti</option>
                <option value="bidang" {{ old('team_type') === 'bidang' ? 'selected' : '' }}>Tim Bidang</option>
            </select>
            <x-input-error :messages="$errors->get('team_type')" class="mt-2" />
        </div>

        {{-- Tim Inti --}}
        <div class="mt-4 hidden" id="tim-inti-fields">
            <x-input-label for="jabatan_inti" value="Jabatan Tim Inti" />
            <select name="jabatan_inti" id="jabatan_inti" class="block mt-1 w-full text-gray-900 bg-white">
                <option value="">-- Pilih Jabatan --</option>
                <option value="ketua"         {{ old('jabatan_inti') === 'ketua'         ? 'selected' : '' }}>Ketua</option>
                <option value="wakil_ketua"   {{ old('jabatan_inti') === 'wakil_ketua'   ? 'selected' : '' }}>Wakil Ketua</option>
                <option value="sekretaris_1"  {{ old('jabatan_inti') === 'sekretaris_1'  ? 'selected' : '' }}>Sekretaris 1</option>
                <option value="sekretaris_2"  {{ old('jabatan_inti') === 'sekretaris_2'  ? 'selected' : '' }}>Sekretaris 2</option>
                <option value="bendahara_1"   {{ old('jabatan_inti') === 'bendahara_1'   ? 'selected' : '' }}>Bendahara 1</option>
                <option value="bendahara_2"   {{ old('jabatan_inti') === 'bendahara_2'   ? 'selected' : '' }}>Bendahara 2</option>
            </select>
            <x-input-error :messages="$errors->get('jabatan_inti')" class="mt-2" />
        </div>

        {{-- Tim Bidang: Bidang + Jabatan + Sie --}}
        <div id="tim-bidang-fields" class="mt-4 hidden">
            {{-- Bidang --}}
            <div>
                <x-input-label for="bidang_id" value="Bidang" />
                <select id="bidang_id" name="bidang_id" class="block mt-1 w-full text-gray-900 bg-white">
                    <option value="">-- Pilih Bidang --</option>
                    @foreach ($bidangs as $bidang)
                        <option value="{{ $bidang->id }}" {{ old('bidang_id') == $bidang->id ? 'selected' : '' }}>
                            {{ $bidang->nama_bidang }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('bidang_id')" class="mt-2" />
            </div>

            {{-- Jabatan Tim Bidang --}}
            <div class="mt-4">
                <x-input-label for="jabatan_bidang" value="Jabatan di Bidang" />
                <select id="jabatan_bidang" name="jabatan_bidang" class="block mt-1 w-full text-gray-900 bg-white">
                    <option value="">-- Pilih Jabatan --</option>
                    <option value="ketua_bidang" {{ old('jabatan_bidang') === 'ketua_bidang' ? 'selected' : '' }}>Ketua Bidang</option>
                    <option value="anggota_sie"  {{ old('jabatan_bidang') === 'anggota_sie'  ? 'selected' : '' }}>Anggota Sie</option>
                </select>
                <x-input-error :messages="$errors->get('jabatan_bidang')" class="mt-2" />
            </div>

            {{-- Sie --}}
            <div class="mt-4">
                <x-input-label for="sie_id" value="Sie" />
                <select id="sie_id" name="sie_id" class="block mt-1 w-full text-gray-900 bg-white">
                    <option value="">-- Pilih Sie --</option>
                    {{-- Opsi akan diisi via JavaScript --}}
                </select>
                <x-input-error :messages="$errors->get('sie_id')" class="mt-2" />
            </div>
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md
                      focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               href="{{ route('login') }}">
                {{ __('Sudah terdaftar ?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    @php
        // Data Sie dalam bentuk simple array untuk JS
        $siesData = $sies->map(function ($sie) {
            return [
                'id'        => $sie->id,
                'nama_sie'  => $sie->nama_sie,
                'bidang_id' => $sie->bidang_id,
            ];
        });
        $oldSieId = old('sie_id');
    @endphp

    {{-- JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const teamSelect    = document.getElementById('team_type');
            const intiFields    = document.getElementById('tim-inti-fields');
            const bidangFields  = document.getElementById('tim-bidang-fields');

            const bidangSelect  = document.getElementById('bidang_id');
            const sieSelect     = document.getElementById('sie_id');
            const jabatanInti   = document.getElementById('jabatan_inti');
            const jabatanBidang = document.getElementById('jabatan_bidang');

            const siesData = @json($siesData);
            const oldSieId = "{{ $oldSieId }}";

            function updateTeamFields() {
                const team = teamSelect.value;

                intiFields.classList.add('hidden');
                bidangFields.classList.add('hidden');

                if (team === 'inti') {
                    intiFields.classList.remove('hidden');
                    // reset bidang & sie
                    if (bidangSelect) bidangSelect.value = '';
                    if (sieSelect) {
                        sieSelect.innerHTML = '';
                        const placeholder = document.createElement('option');
                        placeholder.value = '';
                        placeholder.textContent = '-- Pilih Sie --';
                        sieSelect.appendChild(placeholder);
                        sieSelect.disabled = false;
                    }
                } else if (team === 'bidang') {
                    bidangFields.classList.remove('hidden');
                    if (jabatanInti) jabatanInti.value = '';
                }
            }

            function populateSieOptions() {
                if (!sieSelect) return;

                const selectedBidangId = bidangSelect.value;

                sieSelect.innerHTML = '';
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = '-- Pilih Sie --';
                sieSelect.appendChild(placeholder);

                if (!selectedBidangId) {
                    return;
                }

                siesData
                    .filter(sie => String(sie.bidang_id) === String(selectedBidangId))
                    .forEach(sie => {
                        const opt = document.createElement('option');
                        opt.value = sie.id;
                        opt.textContent = sie.nama_sie;
                        if (String(sie.id) === String(oldSieId)) {
                            opt.selected = true;
                        }
                        sieSelect.appendChild(opt);
                    });
            }

            function updateSieEnabled() {
                if (!sieSelect || !jabatanBidang) return;

                const jabatan = jabatanBidang.value;

                if (jabatan === 'ketua_bidang') {
                    sieSelect.value = '';
                    sieSelect.disabled = true;
                } else if (jabatan === 'anggota_sie') {
                    sieSelect.disabled = false;
                } else {
                    sieSelect.disabled = false;
                    sieSelect.value = '';
                }
            }

            // Initial state (berdasarkan old value)
            updateTeamFields();
            populateSieOptions();
            updateSieEnabled();

            // Events
            teamSelect.addEventListener('change', function () {
                updateTeamFields();
            });

            if (bidangSelect) {
                bidangSelect.addEventListener('change', function () {
                    // setiap ganti bidang, reset oldSieId behavior
                    populateSieOptions();
                });
            }

            if (jabatanBidang) {
                jabatanBidang.addEventListener('change', function () {
                    updateSieEnabled();
                });
            }
        });
    </script>
</x-guest-layout>
