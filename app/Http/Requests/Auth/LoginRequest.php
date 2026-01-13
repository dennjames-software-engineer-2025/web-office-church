<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'     => ['required', 'string', 'email'],
            'password'  => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $kedudukan = session('login_kedudukan');

        if (! $kedudukan) {
            throw ValidationException::withMessages([
                'email' => 'Silakan pilih kedudukan terlebih dahulu dari halaman awal.',
            ]);
        }

        $email = (string) $this->input('email');

        // Ambil user berdasarkan email untuk cek kedudukan (sebelum Auth::attempt)
        $user = User::query()->where('email', $email)->first();

        // Jika user ada dan bukan super_admin, wajib match kedudukan
        if ($user && ! $user->hasRole('super_admin')) {
            // Pastikan kolom ini memang ada di tabel users: kedudukan
            if (($user->kedudukan ?? null) !== $kedudukan) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => 'Kedudukan tidak sesuai. Silakan login melalui kedudukan yang benar.',
                ]);
            }
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower((string) $this->input('email')) . '|' . $this->ip()
        );
    }
}
