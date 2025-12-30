<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\UserRegisteredPendingMail;
use App\Models\Bidang;
use App\Models\Sie;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $bidangs = Bidang::where('is_active', true)
        ->orderBy('nama_bidang')
        ->get();

        $sies = Sie::with('bidang')
            ->where('is_active', true)
            ->orderBy('nama_sie')
            ->get();

        return view('auth.register', [
            'bidangs' => $bidangs,
            'sies'    => $sies,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Cari user yang sudah pernah pakai email ini dengan status 'rejected'
    $rejectedUser = User::where('email', $request->email)
        ->where('status', 'rejected')
        ->first();
    
    // Email unik untuk semua user non-deleted
    $emailUniqueRule = Rule::unique('users', 'email')->whereNull('deleted_at');

    // Nama unik untuk semua user non-deleted
    $nameUniqueRule = Rule::unique('users', 'name')->whereNull('deleted_at');

    // Kalau re-register untuk user rejected: ignore ID dia sendiri supaya validasi lolos
    if ($rejectedUser) {
        $emailUniqueRule    = $emailUniqueRule->ignore($rejectedUser->id);
        $nameUniqueRule     = $nameUniqueRule->ignore($rejectedUser->id);
    }

    // Validasi dasar
    $request->validate([
        'name'      => ['required', 'string', 'max:255', $nameUniqueRule],
        'email'     => ['required', 'string', 'lowercase', 'email', 'max:255', $emailUniqueRule],
        'password'  => ['required', 'confirmed', Rules\Password::defaults()],
        'team_type' => ['required', 'in:inti,bidang'],
    ]);

    $user = null;

    if ($request->team_type === 'inti') {

        // Validasi khusus Tim Inti
        $request->validate([
            'jabatan_inti' => 'required|in:ketua,wakil_ketua,sekretaris_1,sekretaris_2,bendahara_1,bendahara_2',
        ]);

        $data = [
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'status'           => 'pending',
            'team_type'        => 'inti',
            'jabatan'          => $request->jabatan_inti,
            'bidang_id'        => null,
            'sie_id'           => null,
            'alasan_ditolak' => null,
        ];

        if ($rejectedUser) {
            // Re-register: update user lama
            $rejectedUser->update($data);
            $user = $rejectedUser;
        } else {
            // Register baru
            $user = User::create($data);
        }

        $user->syncRoles(['tim_inti']); // pakai syncRoles supaya bersih
        // atau assignRole kalau kamu yakin user belum punya role

    } elseif ($request->team_type === 'bidang') {

        // Validasi khusus Tim Bidang
        $request->validate([
            'bidang_id'      => 'required|exists:bidangs,id',
            'jabatan_bidang' => 'required|in:ketua_bidang,anggota_sie',
            'sie_id'         => 'nullable|required_if:jabatan_bidang,anggota_sie|exists:sies,id',
        ]);

        $data = [
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'status'           => 'pending',
            'team_type'        => 'bidang',
            'jabatan'          => $request->jabatan_bidang,
            'bidang_id'        => $request->bidang_id,
            'sie_id'           => $request->jabatan_bidang === 'anggota_sie'
                                    ? $request->sie_id
                                    : null,
            'alasan_ditolak' => null,
        ];

        if ($rejectedUser) {
            $rejectedUser->update($data);
            $user = $rejectedUser;
        } else {
            $user = User::create($data);
        }

        $user->syncRoles(['tim_bidang']);
    }

    if (! $user) {
        abort(400, 'Gagal membuat user. Data tim tidak valid.');
    }

    event(new Registered($user));

    Mail::to($user->email)->send(new UserRegisteredPendingMail($user));

    // (Email notifikasi register akan kita tambahkan setelah ini)
    return redirect()
        ->route('login')
        ->with('status', 'Registrasi berhasil. Akun anda menunggu approval dari Tim Inti / Super Admin.');
    }
}
