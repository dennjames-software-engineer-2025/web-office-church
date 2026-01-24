<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bidang;
use App\Models\Sie;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()
            ->with(['bidang', 'sie'])
            ->whereNull('deleted_at')
            ->orderBy('name');

        if ($request->filled('kedudukan')) {
            $query->where('kedudukan', $request->kedudukan);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('jabatan', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $bidangs = Bidang::query()
            ->select('id','nama_bidang','kedudukan','is_active')
            ->where('is_active', true)
            ->orderBy('kedudukan')
            ->orderBy('nama_bidang')
            ->get();

        $sies = Sie::query()
            ->select('id','nama_sie','bidang_id','is_active')
            ->where('is_active', true)
            ->orderBy('nama_sie')
            ->get();

        $lingkunganMap = $this->lingkunganMap();

        return view('users.create-inti', compact('bidangs', 'sies', 'lingkunganMap'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim((string)$request->email)),
            'name'  => trim((string)$request->name),
        ]);

        $allowedByKedudukan = $this->allowedByKedudukan();

        $messages = [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'kedudukan.required' => 'Kedudukan wajib dipilih.',
            'jabatan.required' => 'Jabatan wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'bidang_id.exists' => 'Bidang tidak valid untuk kedudukan tersebut.',
            'sie_id.exists' => 'Sie tidak valid untuk bidang tersebut.',
        ];

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'kedudukan' => ['required', Rule::in(array_keys($allowedByKedudukan))],
            'jabatan'   => ['required', 'string'],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],

            // normal flow (bidang/sie)
            'bidang_id' => [
                'nullable',
                'integer',
                Rule::exists('bidangs', 'id')->where(fn($q) => $q->where('kedudukan', $request->kedudukan)),
            ],
            'sie_id'    => [
                'nullable',
                'integer',
                Rule::exists('sies', 'id')->where(fn($q) => $q->where('bidang_id', $request->bidang_id)),
            ],

            // lingkungan flow (scope/wilayah/lingkungan)
            'lingkungan_scope' => ['nullable', 'string'],
            'wilayah'          => ['nullable', 'string'],
            'lingkungan'       => ['nullable', 'string'],
        ], $messages);

        // ==========================
        // ✅ KHUSUS KEDUDUKAN LINGKUNGAN (BRIEF BARU)
        // ==========================
        if ($validated['kedudukan'] === 'lingkungan') {
            return $this->storeLingkungan($request, $validated);
        }

        // ==========================
        // ✅ KEDUDUKAN NON-LINGKUNGAN (FLOW LAMA)
        // ==========================
        if (! in_array($validated['jabatan'], $allowedByKedudukan[$validated['kedudukan']] ?? [], true)) {
            throw ValidationException::withMessages([
                'jabatan' => 'Jabatan tidak sesuai dengan kedudukan yang dipilih.',
            ]);
        }

        [$bidangId, $sieId] = $this->resolveBidangSieOrFail($validated);

        $role = $this->mapJabatanToRole($validated['jabatan']);
        if (! $role) {
            throw ValidationException::withMessages([
                'jabatan' => 'Jabatan tidak valid.',
            ]);
        }

        return DB::transaction(function () use ($validated, $role, $bidangId, $sieId) {

            $jabatanKey = $this->makeJabatanKey($validated['jabatan'], $bidangId, $sieId);

            $this->assertUniqueJabatanKey(
                kedudukan: $validated['kedudukan'],
                jabatanKey: $jabatanKey,
                ignoreUserId: null,
                field: $this->fieldForUniqueError($validated['jabatan'])
            );

            $user = User::create([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'status'     => 'active',
                'kedudukan'  => $validated['kedudukan'],
                'jabatan'    => $validated['jabatan'],
                'jabatan_key'=> $jabatanKey,
                'team_type'  => null,
                'bidang_id'  => $bidangId,
                'sie_id'     => $sieId,

                // lingkungan fields null
                'lingkungan_scope' => null,
                'wilayah'          => null,
                'lingkungan'       => null,
            ]);

            $user->syncRoles([$role]);

            return redirect()->route('users.index')->with('status', 'Akun berhasil dibuat.');
        });
    }

    public function edit(User $user): View
    {
        $bidangs = Bidang::query()
            ->select('id','nama_bidang','kedudukan','is_active')
            ->where('is_active', true)
            ->orderBy('kedudukan')
            ->orderBy('nama_bidang')
            ->get();

        $sies = Sie::query()
            ->select('id','nama_sie','bidang_id','is_active')
            ->where('is_active', true)
            ->orderBy('nama_sie')
            ->get();

        $lingkunganMap = $this->lingkunganMap();

        return view('users.edit', compact('user', 'bidangs', 'sies', 'lingkunganMap'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim((string)$request->email)),
            'name'  => trim((string)$request->name),
        ]);

        $allowedByKedudukan = $this->allowedByKedudukan();

        $messages = [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'status.required' => 'Status wajib dipilih.',
            'kedudukan.required' => 'Kedudukan wajib dipilih.',
            'jabatan.required' => 'Jabatan wajib dipilih.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'bidang_id.exists' => 'Bidang tidak valid untuk kedudukan tersebut.',
            'sie_id.exists' => 'Sie tidak valid untuk bidang tersebut.',
        ];

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => [
                'required','email','max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($user->id),
            ],
            'status'    => ['required', 'in:active,suspended,pending,rejected'],
            'kedudukan' => ['required', Rule::in(array_keys($allowedByKedudukan))],
            'jabatan'   => ['required', 'string'],

            'bidang_id' => [
                'nullable',
                'integer',
                Rule::exists('bidangs', 'id')->where(fn($q) => $q->where('kedudukan', $request->kedudukan)),
            ],
            'sie_id'    => [
                'nullable',
                'integer',
                Rule::exists('sies', 'id')->where(fn($q) => $q->where('bidang_id', $request->bidang_id)),
            ],

            'password'  => ['nullable', 'confirmed', Rules\Password::defaults()],

            // lingkungan flow
            'lingkungan_scope' => ['nullable', 'string'],
            'wilayah'          => ['nullable', 'string'],
            'lingkungan'       => ['nullable', 'string'],
        ], $messages);

        // ==========================
        // ✅ KHUSUS KEDUDUKAN LINGKUNGAN (BRIEF BARU)
        // ==========================
        if ($validated['kedudukan'] === 'lingkungan') {
            return $this->updateLingkungan($request, $validated, $user);
        }

        // ==========================
        // ✅ KEDUDUKAN NON-LINGKUNGAN (FLOW LAMA)
        // ==========================
        if (! in_array($validated['jabatan'], $allowedByKedudukan[$validated['kedudukan']] ?? [], true)) {
            throw ValidationException::withMessages([
                'jabatan' => 'Jabatan tidak sesuai dengan kedudukan yang dipilih.',
            ]);
        }

        [$bidangId, $sieId] = $this->resolveBidangSieOrFail($validated);

        $role = $this->mapJabatanToRole($validated['jabatan']);
        if (! $role) {
            throw ValidationException::withMessages([
                'jabatan' => 'Jabatan tidak valid.',
            ]);
        }

        return DB::transaction(function () use ($user, $validated, $bidangId, $sieId, $role) {

            $jabatanKey = $this->makeJabatanKey($validated['jabatan'], $bidangId, $sieId);

            $this->assertUniqueJabatanKey(
                kedudukan: $validated['kedudukan'],
                jabatanKey: $jabatanKey,
                ignoreUserId: $user->id,
                field: $this->fieldForUniqueError($validated['jabatan'])
            );

            $user->name        = $validated['name'];
            $user->email       = $validated['email'];
            $user->status      = $validated['status'];
            $user->kedudukan   = $validated['kedudukan'];
            $user->jabatan     = $validated['jabatan'];
            $user->jabatan_key = $jabatanKey;

            $user->bidang_id   = $bidangId;
            $user->sie_id      = $sieId;
            $user->team_type   = null;

            // reset lingkungan fields
            $user->lingkungan_scope = null;
            $user->wilayah          = null;
            $user->lingkungan       = null;

            if (! empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();
            $user->syncRoles([$role]);

            return redirect()->route('users.show', $user)->with('status', 'Data user berhasil diperbarui.');
        });
    }

    public function show(User $user): View
    {
        $user->load(['bidang', 'sie', 'roles']);
        return view('users.show', compact('user'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri');
        }

        $user->delete();
        return redirect()->route('users.index')->with('status', 'Akun User berhasil dihapus');
    }

    // ======================================================================
    // ✅ LINGKUNGAN HANDLERS
    // ======================================================================

    private function storeLingkungan(Request $request, array $validated): RedirectResponse
    {
        $map = $this->lingkunganMap();

        $scope     = (string) $request->input('lingkungan_scope');
        $wilayah   = (string) $request->input('wilayah');
        $lingkungan= $request->input('lingkungan');

        if (! in_array($scope, ['wilayah','lingkungan'], true)) {
            throw ValidationException::withMessages([
                'lingkungan_scope' => 'Scope wajib dipilih: Wilayah atau Lingkungan.',
            ]);
        }

        if (! array_key_exists($wilayah, $map)) {
            throw ValidationException::withMessages([
                'wilayah' => 'Wilayah tidak valid.',
            ]);
        }

        if ($scope === 'lingkungan') {
            if (empty($lingkungan)) {
                throw ValidationException::withMessages([
                    'lingkungan' => 'Lingkungan wajib dipilih.',
                ]);
            }
            if (! in_array($lingkungan, $map[$wilayah], true)) {
                throw ValidationException::withMessages([
                    'lingkungan' => 'Lingkungan tidak sesuai dengan wilayah yang dipilih.',
                ]);
            }
        } else {
            $lingkungan = null;
        }

        $allowed = $this->allowedJabatanLingkunganByScope();
        if (! in_array($validated['jabatan'], $allowed[$scope] ?? [], true)) {
            throw ValidationException::withMessages([
                'jabatan' => 'Jabatan tidak sesuai dengan Scope yang dipilih.',
            ]);
        }

        $role = $this->mapJabatanToRole($validated['jabatan']);
        if (! $role) {
            throw ValidationException::withMessages([
                'jabatan' => 'Jabatan tidak valid.',
            ]);
        }

        $jabatanKey = $this->makeJabatanKeyForLingkungan($scope, $wilayah, $lingkungan, $validated['jabatan']);

        return DB::transaction(function () use ($validated, $role, $scope, $wilayah, $lingkungan, $jabatanKey) {

            $this->assertUniqueJabatanKey(
                kedudukan: 'lingkungan',
                jabatanKey: $jabatanKey,
                ignoreUserId: null,
                field: 'jabatan'
            );

            $user = User::create([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'status'     => 'active',
                'kedudukan'  => 'lingkungan',
                'jabatan'    => $validated['jabatan'],
                'jabatan_key'=> $jabatanKey,

                'team_type'  => null,
                'bidang_id'  => null,
                'sie_id'     => null,

                'lingkungan_scope' => $scope,
                'wilayah'          => $wilayah,
                'lingkungan'       => $lingkungan,
            ]);

            $user->syncRoles([$role]);

            return redirect()->route('users.index')->with('status', 'Akun berhasil dibuat.');
        });
    }

    private function updateLingkungan(Request $request, array $validated, User $user): RedirectResponse
    {
        $map = $this->lingkunganMap();

        $scope     = (string) $request->input('lingkungan_scope');
        $wilayah   = (string) $request->input('wilayah');
        $lingkungan= $request->input('lingkungan');

        if (! in_array($scope, ['wilayah','lingkungan'], true)) {
            throw ValidationException::withMessages([
                'lingkungan_scope' => 'Scope wajib dipilih: Wilayah atau Lingkungan.',
            ]);
        }

        if (! array_key_exists($wilayah, $map)) {
            throw ValidationException::withMessages([
                'wilayah' => 'Wilayah tidak valid.',
            ]);
        }

        if ($scope === 'lingkungan') {
            if (empty($lingkungan)) {
                throw ValidationException::withMessages([
                    'lingkungan' => 'Lingkungan wajib dipilih.',
                ]);
            }
            if (! in_array($lingkungan, $map[$wilayah], true)) {
                throw ValidationException::withMessages([
                    'lingkungan' => 'Lingkungan tidak sesuai dengan wilayah yang dipilih.',
                ]);
            }
        } else {
            $lingkungan = null;
        }

        $allowed = $this->allowedJabatanLingkunganByScope();
        if (! in_array($validated['jabatan'], $allowed[$scope] ?? [], true)) {
            throw ValidationException::withMessages([
                'jabatan' => 'Jabatan tidak sesuai dengan Scope yang dipilih.',
            ]);
        }

        $role = $this->mapJabatanToRole($validated['jabatan']);
        if (! $role) {
            throw ValidationException::withMessages([
                'jabatan' => 'Jabatan tidak valid.',
            ]);
        }

        $jabatanKey = $this->makeJabatanKeyForLingkungan($scope, $wilayah, $lingkungan, $validated['jabatan']);

        return DB::transaction(function () use ($user, $validated, $role, $scope, $wilayah, $lingkungan, $jabatanKey) {

            $this->assertUniqueJabatanKey(
                kedudukan: 'lingkungan',
                jabatanKey: $jabatanKey,
                ignoreUserId: $user->id,
                field: 'jabatan'
            );

            $user->name        = $validated['name'];
            $user->email       = $validated['email'];
            $user->status      = $validated['status'];
            $user->kedudukan   = 'lingkungan';
            $user->jabatan     = $validated['jabatan'];
            $user->jabatan_key = $jabatanKey;

            $user->team_type   = null;
            $user->bidang_id   = null;
            $user->sie_id      = null;

            $user->lingkungan_scope = $scope;
            $user->wilayah          = $wilayah;
            $user->lingkungan       = $lingkungan;

            if (! empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();
            $user->syncRoles([$role]);

            return redirect()->route('users.show', $user)->with('status', 'Data user berhasil diperbarui.');
        });
    }

    // ======================================================================
    // Helpers
    // ======================================================================

    private function allowedByKedudukan(): array
    {
        return [
            'dpp_inti' => ['ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2','ketua_bidang','ketua_sie'],
            'bgkp' => ['ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2','ketua_bidang'],
            'lingkungan' => ['__handled_by_scope__'], // ✅ jangan list jabatan lingkungan di sini
            'sekretariat' => ['sekretariat'],
        ];
    }

    private function lingkunganMap(): array
    {
        return config('lingkungan', []);
    }

    private function allowedJabatanLingkunganByScope(): array
    {
        return [
            'wilayah' => [
                'fasilitator_wilayah',
                'sekretaris_wilayah',
                'bendahara_wilayah',
                'sie_biak_wilayah',
                'sie_omk_wilayah',
                'sie_keluarga_wilayah',
                'sie_lansia_wilayah',
                'sie_tatib_kolektan_wilayah',
            ],
            'lingkungan' => [
                'ketua_lingkungan',
                'wakil_ketua_lingkungan',
                'sekretaris_lingkungan',
                'bendahara_lingkungan',
                'seksi_liturgi',
                'seksi_katekese',
                'seksi_kerasulan_kitab_suci',
                'seksi_sosial',
                'seksi_pengabdian_masyarakat',
                'pelayanan_kematian',
            ],
        ];
    }

    private function mapJabatanToRole(string $jabatan): ?string
    {
        $lingkunganJabatan = array_merge(
            $this->allowedJabatanLingkunganByScope()['wilayah'],
            $this->allowedJabatanLingkunganByScope()['lingkungan'],
        );

        return match (true) {
            $jabatan === 'ketua' => 'ketua',
            $jabatan === 'wakil_ketua' => 'wakil_ketua',
            str_starts_with($jabatan, 'sekretaris') => 'sekretaris',
            str_starts_with($jabatan, 'bendahara') => 'bendahara',

            $jabatan === 'anggota_komunitas' => 'anggota_komunitas',
            $jabatan === 'sekretariat' => 'sekretariat',
            $jabatan === 'ketua_bidang' => 'ketua_bidang',
            $jabatan === 'ketua_sie' => 'ketua_sie',

            // ✅ jabatan lingkungan: role = nama jabatan
            in_array($jabatan, $lingkunganJabatan, true) => $jabatan,

            default => null,
        };
    }

    private function resolveBidangSieOrFail(array &$validated): array
    {
        $jabatan = $validated['jabatan'];

        $needsBidang = in_array($jabatan, ['ketua_bidang','ketua_sie'], true);
        $needsSie    = ($jabatan === 'ketua_sie');

        $bidangId = null;
        $sieId    = null;

        if (! $needsBidang) {
            return [$bidangId, $sieId];
        }

        if (empty($validated['bidang_id'])) {
            throw ValidationException::withMessages([
                'bidang_id' => 'Bidang wajib dipilih untuk jabatan ini.',
            ]);
        }
        $bidangId = (int) $validated['bidang_id'];

        if (! $needsSie) {
            return [$bidangId, $sieId];
        }

        if (empty($validated['sie_id'])) {
            throw ValidationException::withMessages([
                'sie_id' => 'Sie wajib dipilih untuk Ketua Sie.',
            ]);
        }
        $sieId = (int) $validated['sie_id'];

        return [$bidangId, $sieId];
    }

    private function makeJabatanKey(string $jabatan, ?int $bidangId, ?int $sieId): string
    {
        return match ($jabatan) {
            'ketua_bidang' => "ketua_bidang#bidang:" . ($bidangId ?? 'null'),
            'ketua_sie'    => "ketua_sie#bidang:" . ($bidangId ?? 'null') . "#sie:" . ($sieId ?? 'null'),
            default        => $jabatan,
        };
    }

    private function makeJabatanKeyForLingkungan(string $scope, string $wilayah, ?string $lingkungan, string $jabatan): string
    {
        if ($scope === 'wilayah') {
            return "lingkungan#scope:wilayah#wilayah:{$wilayah}#jabatan:{$jabatan}";
        }

        $slug = str($lingkungan ?? '')->slug('-')->toString();
        return "lingkungan#scope:lingkungan#wilayah:{$wilayah}#lingkungan:{$slug}#jabatan:{$jabatan}";
    }

    private function fieldForUniqueError(string $jabatan): string
    {
        return match ($jabatan) {
            'ketua_bidang' => 'bidang_id',
            'ketua_sie'    => 'sie_id',
            default        => 'jabatan',
        };
    }

    private function assertUniqueJabatanKey(
        string $kedudukan,
        string $jabatanKey,
        ?int $ignoreUserId,
        string $field
    ): void {
        $q = User::query()
            ->whereNull('deleted_at')
            ->where('kedudukan', $kedudukan)
            ->where('jabatan_key', $jabatanKey);

        if ($ignoreUserId) {
            $q->where('id', '!=', $ignoreUserId);
        }

        $exists = $q->lockForUpdate()->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                $field => 'Jabatan ini sudah dipakai oleh user lain pada kedudukan yang sama. Tidak boleh dobel.',
            ]);
        }
    }
}
