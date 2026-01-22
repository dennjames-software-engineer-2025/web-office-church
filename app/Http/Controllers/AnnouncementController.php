<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $canManage = $user->canManageAnnouncements();

        $q = trim((string) $request->query('q', ''));

        $items = Announcement::with('creator')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('body', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->get()
            ->filter(function (Announcement $a) use ($user, $canManage) {

                // 1) Kalau user bisa manage -> biarkan lihat (biar sekretaris/admin tidak kehilangan list)
                if ($canManage) return true;

                // 2) User biasa: hanya tampil yang active now
                if (! $a->isActiveNow()) return false;

                // helper normalize kedudukan
                $normalize = function (?string $val): ?string {
                    if (!$val) return null;
                    $v = strtolower(trim($val));
                    $v = str_replace([' ', '-'], '_', $v);

                    // mapping aman
                    $map = [
                        'dppinti' => 'dpp_inti',
                        'dpp_inti' => 'dpp_inti',
                        'bgkp' => 'bgkp',
                        'lingkungan' => 'lingkungan',
                        'sekretariat' => 'sekretariat',
                    ];

                    $v2 = str_replace('_', '', $v);
                    return $map[$v] ?? $map[$v2] ?? $v;
                };

                // 3) Filter target kedudukan
                $tk = $a->target_kedudukan ?? [];
                $tk = array_values(array_filter(array_map(fn($x) => $normalize((string)$x), $tk)));

                if (!empty($tk)) {
                    $userK = $normalize($user->kedudukan);

                    // kalau user kedudukan kosong => tidak lolos (ini yg bikin user kamu "hilang")
                    if (!$userK) return false;

                    if (!in_array($userK, $tk, true)) return false;
                }

                // 4) Filter target roles
                $tr = $a->target_roles ?? [];
                $tr = array_values(array_filter(array_map(fn($x) => strtolower(trim((string)$x)), $tr)));

                if (!empty($tr)) {
                    $userRoles = $user->getRoleNames()->map(fn($r) => strtolower((string)$r))->toArray();
                    if (count(array_intersect($tr, $userRoles)) === 0) return false;
                }

                return true;
            });

        return view('announcements.index', compact('items', 'q', 'canManage'));
    }

    public function create(): View
    {
        $user = Auth::user();
        abort_unless($user->canManageAnnouncements(), 403);

        $kedudukanOptions = [
            'dpp_inti' => 'DPP Inti',
            'bgkp' => 'BGKP',
            'lingkungan' => 'Lingkungan',
            'sekretariat' => 'Sekretariat',
        ];

        $roleOptions = [
            'super_admin',
            'ketua',
            'wakil_ketua',
            'sekretaris',
            'bendahara',
            'ketua_bidang',
            'ketua_sie',
            'ketua_lingkungan',
            'wakil_ketua_lingkungan',
            'anggota_komunitas',
        ];

        return view('announcements.create', compact('kedudukanOptions', 'roleOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->canManageAnnouncements(), 403);

        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'body' => ['required','string'],
            'status' => ['required','in:draft,published'],
            'is_pinned' => ['nullable','boolean'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],

            'target_kedudukan' => ['nullable','array'],
            'target_kedudukan.*' => ['string'],

            'target_roles' => ['nullable','array'],
            'target_roles.*' => ['string'],
        ]);

        $a = Announcement::create([
            'created_by' => $user->id,
            'title' => $data['title'],
            'body' => $data['body'],
            'status' => $data['status'],
            'is_pinned' => (bool)($data['is_pinned'] ?? false),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'target_kedudukan' => $data['target_kedudukan'] ?? [],
            'target_roles' => $data['target_roles'] ?? [],
        ]);

        return redirect()->route('announcements.show', $a)->with('status', 'Pengumuman berhasil dibuat.');
    }

    public function show(Announcement $announcement): View
    {
        $announcement->load('creator');
        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement): View
    {
        $user = Auth::user();
        abort_unless($user->canManageAnnouncements(), 403);

        $kedudukanOptions = [
            'dpp_inti' => 'DPP Inti',
            'bgkp' => 'BGKP',
            'lingkungan' => 'Lingkungan',
            'sekretariat' => 'Sekretariat',
        ];

        $roleOptions = [
            'super_admin',
            'ketua',
            'wakil_ketua',
            'sekretaris',
            'bendahara',
            'ketua_bidang',
            'ketua_sie',
            'ketua_lingkungan',
            'wakil_ketua_lingkungan',
            'anggota_komunitas',
        ];

        return view('announcements.edit', compact('announcement', 'kedudukanOptions', 'roleOptions'));
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->canManageAnnouncements(), 403);

        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'body' => ['required','string'],
            'status' => ['required','in:draft,published'],
            'is_pinned' => ['nullable','boolean'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],

            'target_kedudukan' => ['nullable','array'],
            'target_kedudukan.*' => ['string'],

            'target_roles' => ['nullable','array'],
            'target_roles.*' => ['string'],
        ]);

        $announcement->update([
            'title' => $data['title'],
            'body' => $data['body'],
            'status' => $data['status'],
            'is_pinned' => (bool)($data['is_pinned'] ?? false),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'target_kedudukan' => $data['target_kedudukan'] ?? [],
            'target_roles' => $data['target_roles'] ?? [],
        ]);

        return redirect()->route('announcements.show', $announcement)->with('status', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->canManageAnnouncements(), 403);

        $announcement->delete();
        return redirect()->route('announcements.index')->with('status', 'Pengumuman berhasil dihapus.');
    }
}
