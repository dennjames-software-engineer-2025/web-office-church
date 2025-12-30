<?php

namespace App\Http\Controllers;

use App\Mail\UserApprovedMail;
use App\Mail\UserRejectedMail;
use App\Mail\UserRegisteredPendingMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserApprovalController extends Controller
{
    public function index(): View
    {
        $pendingUsers = User::where('status', 'pending')->with(['bidang', 'sie'])->get();

        return view('users.pending', compact('pendingUsers'));
    }

    public function approve(User $user): RedirectResponse
    {
        if ($user->status !== 'pending') {
            return back()->with('status', 'User ini tidak dalam status pending');
        }

        $user->status = 'active';
        $user->save();

        Mail::to($user->email)->send(new UserApprovedMail($user));

        return back()->with('status', 'User berhasil di Approve');
    }

    public function reject(User $user, Request $request): RedirectResponse
    {
        // Validasi alasan penolakan
        $data = $request->validate([
            'alasan' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->status = 'rejected';
        $user->alasan_ditolak = $data['alasan'] ?? null;
        $user->save();

        Mail::to($user->email)->send(new UserRejectedMail($user));

        return back()->with('status', 'User berhasil ditolak');
    }
}
