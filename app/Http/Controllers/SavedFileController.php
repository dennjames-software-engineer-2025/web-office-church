<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Folder;
use App\Models\Document;
use App\Models\Template;
use App\Models\SavedFile;
use App\Models\ProposalFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedFileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:files.manage')->only(['store','update','destroy']);
        $this->middleware('permission:files.view')->only(['sharedIndex']);
    }

    /**
     * Tambahkan dokumen ke folder (sekretaris).
     * source dapat: template / proposal_file
     */
    public function store(Request $request, Folder $folder)
    {
        $this->authorize('manage', SavedFile::class);

        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            // ok
        } else {
            $isSek12DppInti = $user->hasRole('sekretaris')
                && $user->kedudukan === 'dpp_inti'
                && in_array($user->jabatan, ['sekretaris_1','sekretaris_2'], true);

            if ($isSek12DppInti) {
                $sekIds = User::role('sekretaris')
                    ->where('kedudukan', 'dpp_inti')
                    ->whereIn('jabatan', ['sekretaris_1','sekretaris_2'])
                    ->pluck('id');

                abort_unless($sekIds->contains((int)$folder->created_by), 403);
            } else {
                abort_unless((int)$folder->created_by === (int)$user->id, 403);
            }
        }

        $data = $request->validate([
            'source' => ['required', 'in:template,proposal_file,document'],
            'source_id' => ['required', 'integer'],
        ]);

        $sourceType = match ($data['source']) {
            'template' => Template::class,
            'proposal_file' => ProposalFile::class,
            'document' => Document::class,
        };

        // pastikan sumbernya ada
        $exists = $sourceType::whereKey($data['source_id'])->exists();
        abort_unless($exists, 404);

        // cegah dobel masuk folder
        $already = SavedFile::where('folder_id', $folder->id)
            ->where('source_type', $sourceType)
            ->where('source_id', $data['source_id'])
            ->exists();

        if ($already) {
            return back()->with('error', 'Dokumen ini sudah ada di folder.');
        }

        SavedFile::create([
            'folder_id' => $folder->id,
            'source_type' => $sourceType,
            'source_id' => $data['source_id'],
            'is_private' => true, // default private
            'shared_roles' => [],
            'shared_bidang_ids' => [],
            'added_by' => $user->id,
        ]);

        return back()->with('status', 'Dokumen ditambahkan ke folder.');
    }

    /**
     * Update setting: private / share roles / share bidang
     */
    public function update(Request $request, SavedFile $item)
    {
        $this->authorize('manage', SavedFile::class);

        $data = $request->validate([
            'is_private' => ['required', 'boolean'],
            'shared_roles' => ['nullable', 'array'],
            'shared_roles.*' => ['string'],
            'shared_bidang_ids' => ['nullable', 'array'],
            'shared_bidang_ids.*' => ['integer'],
        ]);

        $isPrivate = (bool) $data['is_private'];

        $roles = $isPrivate ? [] : array_values(array_unique($data['shared_roles'] ?? []));
        $bidangIds = $isPrivate ? [] : array_values(array_unique(array_map('intval', $data['shared_bidang_ids'] ?? [])));

        $item->update([
            'is_private' => $isPrivate,
            'shared_roles' => $roles,
            'shared_bidang_ids' => $bidangIds,
        ]);

        return back()->with('status', 'Setting dokumen berhasil diperbarui.');
    }

    public function destroy(SavedFile $item)
    {
        $this->authorize('manage', SavedFile::class);

        $item->delete();

        return back()->with('status', 'Dokumen dihapus dari folder.');
    }

    /**
     * Halaman publik untuk role lain: lihat dokumen yang dishare ke dia
     */
    public function sharedIndex()
    {
        $user = Auth::user();
        abort_unless($user->can('files.view'), 403);

        $items = SavedFile::query()
            ->where('is_private', false)
            ->with('source')
            ->latest()
            ->get()
            ->filter(fn ($item) => $user->can('view', $item))
            ->values();

        return view('files.shared.index', compact('items'));
    }
}
