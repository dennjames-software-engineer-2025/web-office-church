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
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $emailUniqueRule = Rule::unique(User::class)->ignore($user->id);

        $jabatanUniqueRule = Rule::unique('users', 'jabatan')
        ->where(fn($q) => $q
            ->whereNull('deleted_at')
            ->where('team_type', 'inti')
            ->where('status', '!=', 'rejected')
        )
        ->ignore($user->id); // abaikan user yg sedang di-edit

        // Validasi dasar
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'lowercase', 'email', 'max:255', $emailUniqueRule],
            'status'    => ['required', 'in:pending,active,rejected,suspended'],
            'jabatan_inti' => [
                'required',
                'in:ketua,wakil_ketua,sekretaris_1,sekretaris_2,bendahara_1,bendahara_2', $jabatanUniqueRule
            ],
        ]);

        // Semua user sekarang Tim Inti
        $user->update([
            'name'   => $request->name,
            'email'  => $request->email,
            'status' => $request->status,

            // kolom Tim Inti
            'team_type' => 'inti',
            'jabatan'   => $request->jabatan_inti,

            // dihapuskan karena tidak dipakai
            'bidang_id' => null,
            'sie_id'    => null,
        ]);

        // assign role
        $user->syncRoles(['tim_inti']);

        return redirect()
            ->route('users.index')
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

    public function createTimInti(): \Illuminate\View\View
    {
        return view('users.create-inti');
    }

    // =====================================================================================================
    // End Method Create
    // =====================================================================================================

    // =====================================================================================================
    // Method Store
    // =====================================================================================================

    public function storeTimInti(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim($request->email)),
            'name'  => trim($request->name),
        ]);

        $emailUniqueRule = Rule::unique('users', 'email')->whereNull('deleted_at');
        $nameUniqueRule  = Rule::unique('users', 'name')->whereNull('deleted_at');
        $jabatanUniqueRule = Rule::unique('users', 'jabatan')
            ->where(fn($q) => $q->whereNull('deleted_at')
                ->where('team_type', 'inti')
                ->where('status', '!=', 'rejected') 
            );

        $validate = $request->validate([
            'name'         => ['required', 'string', 'max:255', $nameUniqueRule],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', $emailUniqueRule],
            'jabatan_inti' => ['required', 'in:ketua,wakil_ketua,sekretaris_1,sekretaris_2,bendahara_1,bendahara_2', $jabatanUniqueRule],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'          => $validate['name'],
            'email'         => $validate['email'],
            'password'      => Hash::make($validate['password']),
            'status'        => 'active',
            'team_type'     => 'inti',
            'jabatan'       => $validate['jabatan_inti'],
            'bidang_id'     => null,
            'sie_id'        => null,
            'alasan_ditolak'=> null,
        ]);

        $user->syncRoles(['tim_inti']);

        return redirect()
            ->route('users.index')
            ->with('status', 'Akun Tim Inti berhasil dibuat dan langsung aktif');
        }
}
