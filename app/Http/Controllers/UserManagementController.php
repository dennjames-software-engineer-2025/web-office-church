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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserDeletedMail;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['bidang', 'sie'])->orderBy('name');

        // Filter berdasarkan Tim Inti
        if ($request->filled('team_type')) {
            $query->where('team_type', $request->team_type);
        }

        // Filter berdasarkan Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination
        $users = $query->paginate(10);

        return view('users.index', compact('users'));
    }

    public function edit(User $user): View
    {
        // ambil semua bidang aktif (untuk dropdown + filter di client)
        $bidangs = Bidang::query()
            ->select('id', 'nama_bidang', 'kedudukan', 'is_active')
            ->where('is_active', true)
            ->orderBy('kedudukan')
            ->orderBy('nama_bidang')
            ->get();

        // ambil semua sie aktif (untuk dropdown + filter by bidang di client)
        $sies = Sie::query()
            ->select('id', 'nama_sie', 'bidang_id', 'is_active')
            ->where('is_active', true)
            ->orderBy('nama_sie')
            ->get();

        return view('users.edit', compact('user', 'bidangs', 'sies'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim((string)$request->email)),
            'name'  => trim((string)$request->name),
        ]);

        // daftar jabatan valid per kedudukan
        // NOTE: ketua_bidang dibuat berlaku untuk dpp_inti,bgkp,lingkungan
        // ketua_sie tetap khusus dpp_inti (karena sie hanya ada di struktur DPP)
        $jabatanByKedudukan = [
            'dpp_inti' => ['ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2','ketua_bidang','ketua_sie'],
            'bgkp'     => ['ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2','ketua_bidang'],
            'lingkungan' => ['ketua_lingkungan','wakil_ketua_lingkungan','anggota_komunitas','ketua_bidang'],
            'sekretariat' => ['sekretariat'],
        ];

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => [
                'required','email','max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($user->id),
            ],
            'status'    => ['required', 'in:pending,active,rejected,suspended'],
            'kedudukan' => ['required', Rule::in(array_keys($jabatanByKedudukan))],
            'jabatan'   => ['required', 'string'],

            // bidang harus sesuai kedudukan
            'bidang_id' => [
                'nullable',
                'integer',
                Rule::exists('bidangs', 'id')->where(fn($q) => $q->where('kedudukan', $request->kedudukan)),
            ],

            // sie harus sesuai bidang_id (kalau bidang_id ada)
            'sie_id'    => [
                'nullable',
                'integer',
                Rule::exists('sies', 'id')->where(fn($q) => $q->where('bidang_id', $request->bidang_id)),
            ],

            // password opsional saat edit
            'password'  => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // Validasi jabatan sesuai kedudukan
        $allowedJabatan = $jabatanByKedudukan[$validated['kedudukan']] ?? [];
        if (! in_array($validated['jabatan'], $allowedJabatan, true)) {
            return back()
                ->withErrors(['jabatan' => 'Jabatan tidak sesuai dengan kedudukan yang dipilih.'])
                ->withInput();
        }

        // Aturan kebutuhan bidang/sie
        $needsBidang = in_array($validated['jabatan'], ['ketua_bidang','ketua_sie'], true);
        $needsSie    = ($validated['jabatan'] === 'ketua_sie');

        $bidangId = null;
        $sieId    = null;

        if ($needsBidang) {
            if (empty($validated['bidang_id'])) {
                return back()->withErrors(['bidang_id' => 'Bidang wajib dipilih untuk jabatan ini.'])->withInput();
            }
            $bidangId = (int) $validated['bidang_id'];
        }

        if ($needsSie) {
            if (empty($validated['sie_id'])) {
                return back()->withErrors(['sie_id' => 'Sie wajib dipilih untuk Ketua Sie.'])->withInput();
            }

            // Karena validation rule sudah memastikan sie_id belongs to bidang_id,
            // di sini cukup set id-nya
            $sieId = (int) $validated['sie_id'];
        }

        // mapping jabatan -> role (harus sama dengan store())
        $role = match (true) {
            $validated['jabatan'] === 'ketua' => 'ketua',
            $validated['jabatan'] === 'wakil_ketua' => 'wakil_ketua',
            str_starts_with($validated['jabatan'], 'sekretaris') => 'sekretaris',
            str_starts_with($validated['jabatan'], 'bendahara') => 'bendahara',
            $validated['jabatan'] === 'ketua_lingkungan' => 'ketua_lingkungan',
            $validated['jabatan'] === 'wakil_ketua_lingkungan' => 'wakil_ketua_lingkungan',
            $validated['jabatan'] === 'anggota_komunitas' => 'anggota_komunitas',
            $validated['jabatan'] === 'sekretariat' => 'sekretariat',
            $validated['jabatan'] === 'ketua_bidang' => 'ketua_bidang',
            $validated['jabatan'] === 'ketua_sie' => 'ketua_sie',
            default => null,
        };

        if (! $role) {
            return back()->withErrors(['jabatan' => 'Jabatan tidak valid.'])->withInput();
        }

        // Update data inti user
        $user->name      = $validated['name'];
        $user->email     = $validated['email'];
        $user->status    = $validated['status'];
        $user->kedudukan = $validated['kedudukan'];
        $user->jabatan   = $validated['jabatan'];

        // Set bidang/sie sesuai kebutuhan
        $user->bidang_id = $bidangId;
        $user->sie_id    = $sieId;

        // team_type boleh kamu putuskan
        $user->team_type = null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Sync roles sesuai jabatan
        $user->syncRoles([$role]);

        return redirect()
            ->route('users.show', $user)
            ->with('status', 'Data user berhasil diperbarui.');
    }

    public function show(User $user): View
    {
        $user->load(['bidang', 'sie', 'roles']);

        return view('users.show', compact('user'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Tidak boleh menghapus akun sendiri
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri');
        }

        $data = $request->validate([
            'alasan_dihapus' => ['required', 'string', 'max:1000'],
        ]);

        $user->alasan_dihapus = $data['alasan_dihapus'];
        $user->save();

        if ($user->email) {
            Mail::to($user->email)->send(new UserDeletedMail($user));
        }

        $user->delete();

        return redirect()->route('users.index')->with('status', 'Akun User berhasil dihapus');
    }

    // =====================================================================================================
    // Method Create
    // =====================================================================================================

    public function create(): View
    {
        // semua bidang aktif + ada kedudukan untuk filter di client
        $bidangs = Bidang::query()
            ->select('id','nama_bidang','kedudukan','is_active')
            ->where('is_active', true)
            ->orderBy('kedudukan')
            ->orderBy('nama_bidang')
            ->get();

        // semua sie aktif, nanti difilter berdasarkan bidang_id
        $sies = Sie::query()
            ->select('id','nama_sie','bidang_id','is_active')
            ->where('is_active', true)
            ->orderBy('nama_sie')
            ->get();

        return view('users.create-inti', compact('bidangs', 'sies'));
    }

    // =====================================================================================================
    // Method Store
    // =====================================================================================================

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim((string)$request->email)),
            'name'  => trim((string)$request->name),
        ]);

        // jabatan yang diperbolehkan per kedudukan
        // NOTE: ketua_bidang dibuat berlaku untuk dpp_inti,bgkp,lingkungan
        $allowedByKedudukan = [
            'dpp_inti' => [
                'ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2',
                'ketua_bidang','ketua_sie',
            ],
            'bgkp' => [
                'ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2',
                'ketua_bidang',
            ],
            'lingkungan' => [
                'ketua_lingkungan','wakil_ketua_lingkungan','anggota_komunitas',
                'ketua_bidang',
            ],
            'sekretariat' => [
                'sekretariat',
            ],
        ];

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'kedudukan' => ['required', Rule::in(array_keys($allowedByKedudukan))],
            'jabatan'   => ['required', 'string'],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],

            // bidang harus sesuai kedudukan
            'bidang_id' => [
                'nullable',
                'integer',
                Rule::exists('bidangs', 'id')->where(fn($q) => $q->where('kedudukan', $request->kedudukan)),
            ],

            // sie harus sesuai bidang_id (kalau bidang_id ada)
            'sie_id'    => [
                'nullable',
                'integer',
                Rule::exists('sies', 'id')->where(fn($q) => $q->where('bidang_id', $request->bidang_id)),
            ],
        ]);

        // kunci jabatan sesuai kedudukan
        if (! in_array($validated['jabatan'], $allowedByKedudukan[$validated['kedudukan']] ?? [], true)) {
            return back()->withErrors(['jabatan' => 'Jabatan tidak sesuai dengan kedudukan yang dipilih.'])->withInput();
        }

        // Aturan kebutuhan bidang/sie
        $needsBidang = in_array($validated['jabatan'], ['ketua_bidang','ketua_sie'], true);
        $needsSie    = ($validated['jabatan'] === 'ketua_sie');

        if (! $needsBidang) {
            $validated['bidang_id'] = null;
            $validated['sie_id'] = null;
        } else {
            if (empty($validated['bidang_id'])) {
                return back()->withErrors(['bidang_id' => 'Bidang wajib dipilih untuk jabatan ini.'])->withInput();
            }
            if (! $needsSie) {
                $validated['sie_id'] = null;
            } else {
                if (empty($validated['sie_id'])) {
                    return back()->withErrors(['sie_id' => 'Sie wajib dipilih untuk Ketua Sie.'])->withInput();
                }
                // rule validation sudah memastikan sie_id belongs to bidang_id
            }
        }

        // mapping jabatan -> role
        $role = match (true) {
            $validated['jabatan'] === 'ketua' => 'ketua',
            $validated['jabatan'] === 'wakil_ketua' => 'wakil_ketua',
            str_starts_with($validated['jabatan'], 'sekretaris') => 'sekretaris',
            str_starts_with($validated['jabatan'], 'bendahara') => 'bendahara',
            $validated['jabatan'] === 'ketua_lingkungan' => 'ketua_lingkungan',
            $validated['jabatan'] === 'wakil_ketua_lingkungan' => 'wakil_ketua_lingkungan',
            $validated['jabatan'] === 'anggota_komunitas' => 'anggota_komunitas',
            $validated['jabatan'] === 'sekretariat' => 'sekretariat',
            $validated['jabatan'] === 'ketua_bidang' => 'ketua_bidang',
            $validated['jabatan'] === 'ketua_sie' => 'ketua_sie',
            default => null,
        };

        if (! $role) {
            return back()->withErrors(['jabatan' => 'Jabatan tidak valid.'])->withInput();
        }

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'status'    => 'active',
            'kedudukan' => $validated['kedudukan'],
            'jabatan'   => $validated['jabatan'],
            'team_type' => null,

            'bidang_id' => $validated['bidang_id'] ?? null,
            'sie_id'    => $validated['sie_id'] ?? null,
        ]);

        $user->syncRoles([$role]);

        return redirect()->route('users.index')->with('status', 'Akun berhasil dibuat.');
    }
}