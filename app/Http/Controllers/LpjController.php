<?php

namespace App\Http\Controllers;

use App\Models\Lpj;
use App\Models\LpjFile;
use App\Models\Proposal;
use App\Models\Folder; // ✅ pastikan model Folder-mu benar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LpjController extends Controller
{
    public function create(Proposal $proposal)
    {
        $user = Auth::user();

        abort_unless($proposal->status === 'approved', 403);
        abort_unless($user->hasRole('ketua_sie') && (int)$proposal->created_by === (int)$user->id, 403);

        // ✅ sementara riwayat dimatikan -> tidak perlu kirim $lpjs
        return view('lpj.create', compact('proposal'));
    }

    public function store(Request $request, Proposal $proposal)
    {
        $user = Auth::user();

        abort_unless($proposal->status === 'approved', 403);
        abort_unless($user->hasRole('ketua_sie') && (int)$proposal->created_by === (int)$user->id, 403);

        $validated = $request->validate([
            'files'     => 'required|array|min:1',
            'files.*'   => 'required|file|max:51200',
            'lpj_name'  => 'required|string|max:255',
            'lpj_date'  => 'required|date',
        ]);

        $lpj = Lpj::create([
            'proposal_id' => $proposal->id,
            'created_by'  => $user->id,
            'bidang_id'   => $proposal->bidang_id,
            'sie_id'      => $proposal->sie_id,
            'status'      => 'menunggu_ketua_bidang',
            'stage'       => 'ketua_bidang',
            'lpj_date'    => $validated['lpj_date'],
            'lpj_name'    => $validated['lpj_name'],
        ]);

        foreach ($request->file('files') as $file) {
            $path = $file->store('lpj_files', 'public');

            LpjFile::create([
                'lpj_id'        => $lpj->id,
                'original_name' => $file->getClientOriginalName(),
                'file_path'     => $path,
                'mime_type'     => $file->getMimeType(),
                'file_size'     => $file->getSize(),
            ]);
        }

        return redirect()
            ->route('lpj.show', [$proposal, $lpj])
            ->with('status', 'LPJ berhasil dikirim dan menunggu persetujuan Ketua Bidang.');
    }

    public function show(Proposal $proposal, Lpj $lpj)
    {
        abort_unless((int)$lpj->proposal_id === (int)$proposal->id, 404);

        $lpj->load(['proposal', 'pengaju', 'bidang', 'sie', 'files']);
        $this->authorize('view', $lpj);

        // ✅ agar sekretaris bisa "+ Folder" pada LPJ
        $folders = collect();
        $user = Auth::user();
        if ($user && $user->can('files.manage')) {
            $folders = Folder::where('created_by', $user->id)->orderBy('name')->get();
        }

        return view('lpj.show', compact('proposal', 'lpj', 'folders'));
    }

    public function index()
    {
        $user = Auth::user();

        $query = Lpj::query()
            ->with([
                'proposal:id,judul,proposal_no,status,stage,created_by,bidang_id,sie_id',
                'pengaju:id,name,email,bidang_id,sie_id,jabatan',
                'bidang:id,nama_bidang',
                'sie:id,nama_sie',
                'files:id,lpj_id,original_name,file_path,mime_type,file_size',
            ])
            ->latest();

        if ($user->hasRole('super_admin')) {
            $lpjs = $query->paginate(10);
            return view('lpj.index', compact('lpjs'));
        }

        if ($user->hasRole('bendahara')) {
            $lpjs = $query->paginate(10);
            return view('lpj.index', compact('lpjs'));
        }

        if ($user->hasRole('ketua_bidang')) {
            $query->where(function ($q) use ($user) {
                $q->where('bidang_id', $user->bidang_id)
                  ->orWhereIn('stage', ['romo', 'bendahara'])
                  ->orWhereIn('status', ['menunggu_romo', 'revisi', 'approved']);
            });

            $lpjs = $query->paginate(10);
            return view('lpj.index', compact('lpjs'));
        }

        if ($user->hasAnyRole(['ketua', 'wakil_ketua', 'sekretaris'])) {

            if ($user->hasRole('sekretaris') && ! $this->isSekretaris1or2($user)) {
                $query->whereRaw('1=0');
                $lpjs = $query->paginate(10);
                return view('lpj.index', compact('lpjs'));
            }

            $query->where(function ($q) {
                $q->where('stage', 'romo')
                  ->orWhere('status', 'approved')
                  ->orWhere('status', 'revisi');
            });

            $lpjs = $query->paginate(10);
            return view('lpj.index', compact('lpjs'));
        }

        $query->where('created_by', $user->id);
        $lpjs = $query->paginate(10);

        return view('lpj.index', compact('lpjs'));
    }

    public function approveKetuaBidang(Proposal $proposal, Lpj $lpj)
    {
        abort_unless((int)$lpj->proposal_id === (int)$proposal->id, 404);
        $this->authorize('approveKetuaBidang', $lpj);

        if ($lpj->status !== 'menunggu_ketua_bidang' || $lpj->stage !== 'ketua_bidang') {
            return back()->with('error', 'LPJ tidak berada di tahap Ketua Bidang.');
        }

        $lpj->update([
            'ketua_bidang_approved_by' => Auth::id(),
            'ketua_bidang_approved_at' => now(),
            'stage'  => 'romo',
            'status' => 'menunggu_romo',
        ]);

        return back()->with('status', 'LPJ disetujui Ketua Bidang dan masuk ke tahap DPP.');
    }

    public function approveFinal(Proposal $proposal, Lpj $lpj)
    {
        abort_unless((int)$lpj->proposal_id === (int)$proposal->id, 404);
        $this->authorize('approveFinal', $lpj);

        if ($lpj->stage !== 'romo' || $lpj->status !== 'menunggu_romo') {
            return back()->with('error', 'LPJ belum berada di tahap final (menunggu DPP).');
        }

        $lpj->update([
            'final_approved_by' => Auth::id(),
            'final_approved_at' => now(),
            'status' => 'approved',
            'stage'  => 'bendahara',
            'reject_reason'  => null,
            'rejected_by'    => null,
            'rejected_at'    => null,
            'rejected_stage' => null,
        ]);

        return back()->with('status', 'LPJ disetujui final oleh DPP.');
    }

    public function rejectFinal(Request $request, Proposal $proposal, Lpj $lpj)
    {
        abort_unless((int)$lpj->proposal_id === (int)$proposal->id, 404);
        $this->authorize('rejectFinal', $lpj);

        $validated = $request->validate([
            'reject_reason' => 'required|string|min:5|max:5000',
        ]);

        if ($lpj->stage !== 'romo' || $lpj->status !== 'menunggu_romo') {
            return back()->with('error', 'LPJ belum berada di tahap final (menunggu DPP).');
        }

        $lpj->update([
            'status' => 'revisi',
            'stage'  => 'sie',
            'reject_reason'  => $validated['reject_reason'],
            'rejected_by'    => Auth::id(),
            'rejected_at'    => now(),
            'rejected_stage' => 'romo',
        ]);

        return back()->with('status', 'LPJ dikembalikan untuk perbaikan.');
    }

    public function addNotes(Request $request, Proposal $proposal, Lpj $lpj)
    {
        abort_unless((int)$lpj->proposal_id === (int)$proposal->id, 404);
        $this->authorize('addNotes', $lpj);

        $validated = $request->validate([
            'notes' => 'required|string|min:3|max:5000',
        ]);

        $newEntry = trim($validated['notes']);

        $lpj->update([
            'notes' => $lpj->notes ? ($lpj->notes . "\n\n" . $newEntry) : $newEntry,
        ]);

        return back()->with('status', 'Notes berhasil ditambahkan.');
    }

    public function preview(Proposal $proposal, Lpj $lpj, LpjFile $lpjFile)
    {
        abort_unless((int)$lpj->proposal_id === (int)$proposal->id, 404);
        abort_unless((int)$lpjFile->lpj_id === (int)$lpj->id, 404);

        $this->authorize('view', $lpj);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($lpjFile->file_path), 404, 'File tidak ditemukan.');

        return response()->file(
            $disk->path($lpjFile->file_path),
            [
                'Content-Type'        => $lpjFile->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.$lpjFile->original_name.'"',
            ]
        );
    }

    public function download(Proposal $proposal, Lpj $lpj, LpjFile $lpjFile)
    {
        abort_unless((int)$lpj->proposal_id === (int)$proposal->id, 404);
        abort_unless((int)$lpjFile->lpj_id === (int)$lpj->id, 404);

        $this->authorize('downloadFile', $lpj);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($lpjFile->file_path), 404, 'File tidak ditemukan.');

        return response()->download(
            $disk->path($lpjFile->file_path),
            $lpjFile->original_name,
            ['Content-Type' => $lpjFile->mime_type ?? 'application/octet-stream']
        );
    }

    private function isSekretaris1or2($user): bool
    {
        if (! $user) return false;
        if (! $user->hasRole('sekretaris')) return false;

        $key = strtolower(trim($user->jabatan_key_active ?? $user->jabatan_key ?? ''));
        if (in_array($key, ['sekretaris_1', 'sekretaris_2'], true)) return true;

        $jabatan = strtolower(trim($user->jabatan ?? ''));
        $jabatan = str_replace([' ', '-'], '_', $jabatan);

        return str_starts_with($jabatan, 'sekretaris_1') || str_starts_with($jabatan, 'sekretaris_2');
    }

    public function destroy(Proposal $proposal, Lpj $lpj)
    {
        abort_unless((int)$lpj->proposal_id === (int)$proposal->id, 404);
        $this->authorize('delete', $lpj);

        $lpj->load('files');
        foreach ($lpj->files as $f) {
            $f->delete();
        }

        $lpj->delete();

        return redirect()->route('lpj.by_proposal', $proposal)->with('status', 'LPJ berhasil dihapus');
    }

    // ✅ Halaman list LPJ khusus 1 proposal (bukan global)
    public function listByProposal(Proposal $proposal)
    {
        $user = Auth::user();

        $lpjs = $proposal->lpjs()
            ->with(['files', 'pengaju:id,name,email', 'bidang:id,nama_bidang', 'sie:id,nama_sie'])
            ->latest()
            ->get()
            ->filter(fn ($lpj) => $user->can('view', $lpj));

        return view('lpj.by_proposal', compact('proposal', 'lpjs'));
    }
}
