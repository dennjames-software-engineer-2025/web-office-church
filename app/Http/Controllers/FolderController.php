<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:files.manage']);
    }

    /**
     * Sekretaris 1/2 di DPP Inti dianggap 1 grup.
     */
    private function isSekretaris12DppInti($user): bool
    {
        if (! $user) return false;

        return $user->hasRole('sekretaris')
            && ($user->kedudukan === 'dpp_inti')
            && in_array($user->jabatan, ['sekretaris_1', 'sekretaris_2'], true);
    }

    /**
     * Ambil user_id Sekretaris 1 & 2 DPP Inti.
     */
    private function sekretaris12DppIntiIds()
    {
        return User::role('sekretaris')
            ->where('kedudukan', 'dpp_inti')
            ->whereIn('jabatan', ['sekretaris_1', 'sekretaris_2'])
            ->pluck('id');
    }

    /**
     * Apakah user boleh akses folder ini?
     */
    private function canAccessFolder(User $user, Folder $folder): bool
    {
        if ($user->hasRole('super_admin')) return true;

        // Sekretaris 1/2 DPP Inti boleh akses folder yang dibuat oleh Sekretaris 1/2 DPP Inti
        if ($this->isSekretaris12DppInti($user)) {
            return $this->sekretaris12DppIntiIds()
                ->contains((int) $folder->created_by);
        }

        // default: hanya pemilik
        return (int) $folder->created_by === (int) $user->id;
    }

    public function index()
    {
        $user = Auth::user();

        $foldersQuery = Folder::query()->latest();

        if ($this->isSekretaris12DppInti($user)) {
            $foldersQuery->whereIn('created_by', $this->sekretaris12DppIntiIds());
        } else {
            $foldersQuery->where('created_by', $user->id);
        }

        $folders = $foldersQuery->get();

        return view('files.folders.index', compact('folders'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Folder::create([
            'name' => $data['name'],
            'created_by' => $user->id,
        ]);

        return redirect()->route('folders.index')->with('status', 'Folder berhasil dibuat.');
    }

    public function show(Folder $folder)
    {
        $user = Auth::user();
        abort_unless($this->canAccessFolder($user, $folder), 403);

        $folder->load([
            'items' => function ($q) {
                $q->latest()->with('source'); // source() kamu sudah withTrashed() ✅
            }
        ]);

        $bidangs = Bidang::orderBy('nama_bidang')->get();

        return view('files.folders.show', compact('folder', 'bidangs'));
    }

    public function destroy(Folder $folder)
    {
        $user = Auth::user();
        abort_unless($this->canAccessFolder($user, $folder), 403);

        $folder->delete();

        return redirect()->route('folders.index')->with('status', 'Folder dihapus.');
    }
}
