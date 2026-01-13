<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
// use League\Config\Exception\ValidationException;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        if (! session()->has('login_kedudukan')) {
        return redirect()
            ->route('welcome.kedudukan')
            ->with('error', 'Silakan pilih kedudukan terlebih dahulu.');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Wajib pilih kedudukan dulu sebelum login
        $selectedKedudukan = session('login_kedudukan');

        if (! $selectedKedudukan) {
            throw ValidationException::withMessages([
                'email' => 'Silakan pilih Kedudukan terlebih dahulu sebelum login.',
            ]);
        }

        $request->authenticate();

        // Mengambil user yang baru login
        $user = $request->user();

        // Memeriksa status User
        if ($user->status !== 'active') {
            Auth::logout();

            $message = match ($user->status) {
                'pending'   => 'Akun anda masih menungu approval dari Tim Inti',
                'rejected'  => 'Registrasi anda telah ditolak. Silahkan hubungi Pengurus',
                'suspended' => 'Akun anda telah dinonaktifkan, Silahkan hubungi Pengurus',
                default     => 'Akun anda belum dapat digunakan',
            };

            throw ValidationException::withMessages([
                'email' => __($message),
            ]);
        }

        // Validasi kedudukan (super_admin bypass)
        if (! $user->hasRole('super_admin')) {

            if (! $user->kedudukan) {
                Auth::logout();

                throw ValidationException::withMessages([
                    'email' => 'Akun ini belum memiliki Kedudukan. Hubungi Super Admin.',
                ]);
            }

            if ($user->kedudukan !== $selectedKedudukan) {
                Auth::logout();

                throw ValidationException::withMessages([
                    'email' => 'Kedudukan login tidak sesuai. Silakan pilih Kedudukan yang benar.',
                ]);
            }
        }

        $request->session()->regenerate();

        $user = $request->user();

        if (! $user->hasRole('super_admin')) {
            $request->session()->forget('url.intended');
            return redirect()->route('dashboard');
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
