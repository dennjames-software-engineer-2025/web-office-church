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
        // ambil semua bidang + sies (buat dropdown)
        $bidangs = Bidang::with('sies')->orderBy('nama_bidang')->get();

        // optional: list sies untuk bidang yang sedang dipilih user
        $sies = $user->bidang_id
            ? Sie::where('bidang_id', $user->bidang_id)->orderBy('nama_sie')->get()
            : collect();

        return view('users.edit', compact('user', 'bidangs', 'sies'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim($request->email)),
            'name'  => trim($request->name),
        ]);

        // daftar jabatan valid per kedudukan
        $jabatanByKedudukan = [
            'dpp_inti' => ['ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2','ketua_bidang','ketua_sie'],
            'bgkp'     => ['ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2'],
            'lingkungan' => ['ketua_lingkungan','wakil_ketua_lingkungan','anggota_komunitas'],
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
            'bidang_id' => ['nullable', 'integer', 'exists:bidangs,id'],
            'sie_id'    => ['nullable', 'integer', 'exists:sies,id'],

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

        // Validasi relasi bidang/sie sesuai jabatan
        $bidangId = null;
        $sieId    = null;

        if ($validated['jabatan'] === 'ketua_bidang') {
            if (empty($validated['bidang_id'])) {
                return back()->withErrors(['bidang_id' => 'Bidang wajib dipilih untuk Ketua Bidang.'])->withInput();
            }
            $bidangId = (int) $validated['bidang_id'];
        }

        if ($validated['jabatan'] === 'ketua_sie') {
            if (empty($validated['sie_id'])) {
                return back()->withErrors(['sie_id' => 'Sie wajib dipilih untuk Ketua Sie.'])->withInput();
            }
            $sie = Sie::with('bidang')->find((int) $validated['sie_id']);
            if (! $sie) {
                return back()->withErrors(['sie_id' => 'Sie tidak valid.'])->withInput();
            }
            $sieId    = $sie->id;
            $bidangId = $sie->bidang_id; // bidang ikut dari sie
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

        // team_type boleh kamu putuskan:
        // kalau mau sederhanakan: null saja
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
        // Memastikan relasi
        $user->load(['bidang', 'sie', 'roles']);

        return view('users.show', compact('user'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Tidak boleh menghapus akun sendiri
        if ($request->user()-> id === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri');
        }

        // Validasi alasan hapus akun 
        $data = $request->validate([
            'alasan_dihapus' => ['required', 'string', 'max:1000'],
        ]);

        // Simpan alasan, lalu menerapkan soft delete
        $user->alasan_dihapus = $data['alasan_dihapus'];
        $user->save();

        // Kirim email ke User 
        if ($user->email) {
            Mail::to($user->email)->send(new UserDeletedMail($user));
        }

        // Implementasi Soft Delete
        $user->delete();

        return redirect()->route('users.index')->with('status', 'Akun User berhasil dihapus');
    }

    // =====================================================================================================
    // Method Create
    // =====================================================================================================

    public function create(): View
    {
        $bidangs = Bidang::orderBy('nama_bidang')->get();
        $sies    = Sie::orderBy('nama_sie')->get(['id','nama_sie','bidang_id']);

        return view('users.create-inti', compact('bidangs', 'sies'));
    }

    // =====================================================================================================
    // End Method Create
    // =====================================================================================================

    // =====================================================================================================
    // Method Store
    // =====================================================================================================

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim($request->email)),
            'name'  => trim($request->name),
        ]);

        // 1) daftar jabatan yang sah
        $allJabatan = [
            // DPP Inti / BGKP
            'ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2',
            // DPP (Bidang/Sie)
            'ketua_bidang','ketua_sie',
            // Lingkungan
            'ketua_lingkungan','wakil_ketua_lingkungan','anggota_komunitas',
            // Sekretariat
            'sekretariat',
        ];

        // 2) jabatan yang diperbolehkan per kedudukan
        $allowedByKedudukan = [
            'dpp_inti' => [
                'ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2',
                'ketua_bidang','ketua_sie',
            ],
            'bgkp' => [
                'ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2',
            ],
            'lingkungan' => [
                'ketua_lingkungan','wakil_ketua_lingkungan','anggota_komunitas',
            ],
            'sekretariat' => [
                'sekretariat',
            ],
        ];

        $validated = $request->validate([
        'name'      => ['required', 'string', 'max:255'],
        'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
        'kedudukan' => ['required', 'in:dpp_inti,bgkp,lingkungan,sekretariat'],
        'jabatan'   => ['required', 'in:ketua,wakil_ketua,sekretaris_1,sekretaris_2,bendahara_1,bendahara_2,ketua_bidang,ketua_sie,ketua_lingkungan,wakil_ketua_lingkungan,anggota_komunitas,sekretariat'],
        'password'  => ['required', 'confirmed', Rules\Password::defaults()],

        'bidang_id' => ['nullable', 'integer', Rule::exists('bidangs', 'id')],
        'sie_id'    => ['nullable', 'integer', Rule::exists('sies', 'id')],
    ]);

    // kunci jabatan sesuai kedudukan
    $allowedByKedudukan = [
        'dpp_inti' => ['ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2','ketua_bidang','ketua_sie'],
        'bgkp' => ['ketua','wakil_ketua','sekretaris_1','sekretaris_2','bendahara_1','bendahara_2'],
        'lingkungan' => ['ketua_lingkungan','wakil_ketua_lingkungan','anggota_komunitas'],
        'sekretariat' => ['sekretariat'],
    ];

    if (!in_array($validated['jabatan'], $allowedByKedudukan[$validated['kedudukan']] ?? [], true)) {
        return back()->withErrors(['jabatan' => 'Jabatan tidak sesuai dengan kedudukan yang dipilih.'])->withInput();
    }

    // aturan bidang/sie
    if ($validated['jabatan'] === 'ketua_bidang') {
        if (empty($validated['bidang_id'])) {
            return back()->withErrors(['bidang_id' => 'Bidang wajib dipilih untuk Ketua Bidang.'])->withInput();
        }
        $validated['sie_id'] = null;
    }

    if ($validated['jabatan'] === 'ketua_sie') {
        if (empty($validated['bidang_id'])) {
            return back()->withErrors(['bidang_id' => 'Bidang wajib dipilih untuk Ketua Sie.'])->withInput();
        }
        if (empty($validated['sie_id'])) {
            return back()->withErrors(['sie_id' => 'Sie wajib dipilih untuk Ketua Sie.'])->withInput();
        }

        $ok = Sie::where('id', $validated['sie_id'])
            ->where('bidang_id', $validated['bidang_id'])
            ->exists();

        if (! $ok) {
            return back()->withErrors(['sie_id' => 'Sie yang dipilih tidak termasuk dalam Bidang tersebut.'])->withInput();
        }
    }

        // 5) mapping jabatan -> role (punyamu sudah OK)
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

        if (!$role) {
            return back()->withErrors(['jabatan' => 'Jabatan tidak valid.'])->withInput();
        }

        // 6) simpan user
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
