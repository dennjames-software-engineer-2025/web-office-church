<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Proposal;
use Illuminate\Support\Str;
use App\Models\ProposalFile;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProposalController extends Controller
{
    /**
     * Helper: Sekretaris 1/2.
     */
    private function isSekretaris1or2($user): bool
    {
        if (! $user) return false;
        if (! $user->hasRole('sekretaris')) return false;

        $jabatan = strtolower(trim($user->jabatan ?? ''));
        $jabatan = str_replace([' ', '-'], '_', $jabatan);

        return in_array($jabatan, ['sekretaris_1', 'sekretaris_2'], true);
    }

    public function index()
    {
        $user = Auth::user();

        $folders = Auth::user()->can('files.manage')
            ? Folder::where('created_by', Auth::id())->orderBy('name')->get()
            : collect();

        // Auto promote DPP Harian -> Romo ketika deadline lewat
        Proposal::query()
            ->where('stage', 'dpp_harian')
            ->whereNotNull('dpp_harian_until')
            ->where('dpp_harian_until', '<=', now())
            ->update([
                'stage'  => 'romo',
                'status' => 'menunggu_romo',
            ]);

        $query = Proposal::query()
            ->with([
                'pengaju:id,name,email,bidang_id,sie_id,jabatan',
                'bidang:id,nama_bidang',
                'sie:id,nama_sie',
                'files:id,proposal_id,original_name,file_path,mime_type,file_size',
            ])
            ->latest();

        if ($user->hasRole('super_admin')) {
            $proposals = $query->paginate(10);
            return view('proposals.index', compact('proposals', 'folders'));
        }

        if ($user->hasRole('bendahara')) {
            $query->where('status', 'approved');
            $proposals = $query->paginate(10);
            return view('proposals.index', compact('proposals', 'folders'));
        }

        if ($user->hasRole('ketua_bidang')) {
            $query->where(function ($q) use ($user) {
                $q->where(function ($qq) use ($user) {
                    $qq->where('stage', 'ketua_bidang')
                        ->where('bidang_id', $user->bidang_id);
                });

                $q->orWhereIn('stage', ['dpp_harian', 'romo', 'bendahara'])
                    ->orWhere('status', 'approved');
            });

            $proposals = $query->paginate(10);
            return view('proposals.index', compact('proposals', 'folders'));
        }

        if ($user->hasAnyRole(['ketua', 'wakil_ketua', 'sekretaris'])) {

            if ($user->hasRole('sekretaris') && ! $this->isSekretaris1or2($user)) {
                $query->whereRaw('1=0');
                $proposals = $query->paginate(10);
                return view('proposals.index', compact('proposals', 'folders'));
            }

            $query->where(function ($q) {
                $q->where('stage', 'dpp_harian')
                    ->orWhere('stage', 'romo')
                    ->orWhere('status', 'approved');
            });

            $proposals = $query->paginate(10);
            return view('proposals.index', compact('proposals', 'folders'));
        }

        // default pengaju lihat miliknya
        $query->where('created_by', $user->id);
        $query->whereNull('archived_at');

        $proposals = $query->paginate(10);
        return view('proposals.index', compact('proposals', 'folders'));
    }

    public function create()
    {
        $this->authorize('create', Proposal::class);
        return view('proposals.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Proposal::class);

        $validated = $request->validate([
            'judul'      => 'required|string|max:255',
            'tujuan'     => 'required|string',
            'files'      => 'required|array|min:1',
            'files.*'    => 'file|mimes:pdf|max:51200',
        ]);

        $user = Auth::user();

        if (!$user->bidang_id || !$user->sie_id) {
            return back()
                ->with('error', 'Akun Ketua Sie harus memiliki Bidang & Sie yang valid.')
                ->withInput();
        }

        $proposal = Proposal::create([
            'created_by' => $user->id,
            'bidang_id'  => $user->bidang_id,
            'sie_id'     => $user->sie_id,
            'judul'      => $validated['judul'],
            'tujuan'     => $validated['tujuan'],
            'status'     => 'menunggu_ketua_bidang',
            'stage'      => 'ketua_bidang',
        ]);

        foreach ($request->file('files') as $file) {
            $path = $file->store('proposal_files', 'public');

            ProposalFile::create([
                'proposal_id'   => $proposal->id,
                'original_name' => $file->getClientOriginalName(),
                'file_path'     => $path,
                'mime_type'     => $file->getMimeType(),
                'file_size'     => $file->getSize(),
            ]);
        }

        return redirect()
            ->route('proposals.index')
            ->with('status', 'Proposal berhasil diajukan dan menunggu persetujuan Ketua Bidang.');
    }

    public function show(Proposal $proposal)
    {
        $proposal->load(['pengaju', 'bidang', 'sie', 'files']);
        $this->authorize('view', $proposal);

        return view('proposals.show', compact('proposal'));
    }

    public function approveKetuaBidang(Proposal $proposal)
    {
        $this->authorize('approveKetuaBidang', $proposal);

        if ($proposal->status !== 'menunggu_ketua_bidang' || $proposal->stage !== 'ketua_bidang') {
            return back()->with('error', 'Proposal tidak berada di tahap Ketua Bidang.');
        }

        $proposal->update([
            'ketua_bidang_approved_by' => Auth::id(),
            'ketua_bidang_approved_at' => now(),
            'status' => 'dpp_harian',
            'stage'  => 'dpp_harian',
            'dpp_harian_until' => now()->addDays(3),
        ]);

        return back()->with('status', 'Proposal di-approve Ketua Bidang dan masuk DPP Harian (default 3 hari).');
    }

    public function setDppDeadline(Request $request, Proposal $proposal)
    {
        $this->authorize('setDppDeadline', $proposal);

        $validated = $request->validate([
            'dpp_days' => 'required|integer|min:1|max:30',
        ]);

        if ($proposal->stage !== 'dpp_harian') {
            return back()->with('error', 'Deadline hanya bisa diubah saat proposal berada di DPP Harian.');
        }

        $proposal->update([
            'dpp_harian_until' => now()->addDays((int)$validated['dpp_days']),
        ]);

        return back()->with('status', 'Durasi DPP Harian berhasil diubah.');
    }

    public function endDppHarian(Proposal $proposal)
    {
        $this->authorize('endDppHarian', $proposal);

        if ($proposal->stage !== 'dpp_harian') {
            return back()->with('error', 'Proposal tidak berada di tahap DPP Harian.');
        }

        $proposal->update([
            'dpp_harian_until' => now(),
            'stage'  => 'romo',
            'status' => 'menunggu_romo',
        ]);

        return back()->with('status', 'Proposal dikirim ke tahap Romo (DPP Harian diselesaikan lebih cepat).');
    }

    /**
     * Notes hanya Sekretaris 1/2 (policy sudah mengatur).
     * Append log (history).
     */
    public function addNotes(Request $request, Proposal $proposal)
    {
        $this->authorize('addNotes', $proposal);

        $validated = $request->validate([
            'notes' => 'required|string|min:3|max:5000',
        ]);

        if (! in_array($proposal->stage, ['dpp_harian', 'romo'], true)) {
            return back()->with('error', 'Notes hanya bisa diberikan saat proposal berada di DPP Harian atau tahap Romo.');
        }

        $user = Auth::user();
        $stamp = now()->format('Y-m-d H:i');
        $actor = $user->name . ' (' . ($user->jabatan ?? $user->getRoleNames()->implode(', ')) . ')';

        $newEntry = "[{$stamp}] {$actor}:\n" . trim($validated['notes']);

        $proposal->update([
            'notes' => $proposal->notes
                ? ($proposal->notes . "\n\n" . $newEntry)
                : $newEntry,
        ]);

        return back()->with('status', 'Notes berhasil ditambahkan (tersimpan sebagai riwayat).');
    }

    public function approveRomo(Proposal $proposal)
    {
        $this->authorize('approveRomo', $proposal);

        if ($proposal->stage !== 'romo' || $proposal->status !== 'menunggu_romo') {
            return back()->with('error', 'Proposal belum berada di tahap Romo.');
        }

        $proposalNo = 'PRP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        $proposal->update([
            'romo_approved_by' => Auth::id(),
            'romo_approved_at' => now(),
            'status'      => 'approved',
            'stage'       => 'bendahara',
            'proposal_no' => $proposalNo,
            'reject_reason'  => null,
            'rejected_by'    => null,
            'rejected_at'    => null,
            'rejected_stage' => null,
        ]);

        $pdf = Pdf::loadView('proposals.receipt', [
            'proposal' => $proposal->fresh(['pengaju','bidang','sie'])
        ]);

        $filename = 'receipts/bukti-penerimaan-' . $proposal->proposal_no . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        $proposal->update([
            'receipt_path' => $filename,
        ]);

        return back()->with('status', 'Proposal di-approve. Bukti penerimaan sudah dibuat dan bisa diunduh.');
    }

    public function rejectRomo(Request $request, Proposal $proposal)
    {
        $this->authorize('rejectRomo', $proposal);

        $validated = $request->validate([
            'reject_reason' => 'required|string|min:5|max:5000',
        ]);

        if ($proposal->stage !== 'romo' || $proposal->status !== 'menunggu_romo') {
            return back()->with('error', 'Proposal belum berada di tahap Romo.');
        }

        $proposal->update([
            'status' => 'revisi',   // ✅ ganti dari ditolak -> revisi
            'stage'  => 'sie',

            'reject_reason'  => $validated['reject_reason'],
            'rejected_by'    => Auth::id(),
            'rejected_at'    => now(),
            'rejected_stage' => 'romo',
        ]);

        return back()->with('status', 'Proposal dikembalikan untuk Revisi (kembali ke pengaju / Sie).');
    }

    public function destroy(Proposal $proposal)
    {
        $this->authorize('delete', $proposal);

        $proposal->load('files');

        foreach ($proposal->files as $f) {
            if ($f->file_path) {
                Storage::disk('public')->delete($f->file_path);
            }
            $f->delete();
        }

        if ($proposal->receipt_path) {
            Storage::disk('public')->delete($proposal->receipt_path);
        }

        $proposal->delete();

        return redirect()->route('proposals.index')->with('status', 'Proposal berhasil dihapus.');
    }

    public function receiptPreview(Proposal $proposal)
    {
        $this->authorize('view', $proposal);

        if (!$proposal->receipt_path) {
            return back()->with('error', 'Bukti penerimaan belum tersedia.');
        }

        $disk = Storage::disk('public');
        abort_unless($disk->exists($proposal->receipt_path), 404);

        return response()->file($disk->path($proposal->receipt_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="bukti-penerimaan-'.$proposal->proposal_no.'.pdf"',
        ]);
    }

    public function receiptDownload(Proposal $proposal)
    {
        $this->authorize('view', $proposal);

        if (!$proposal->receipt_path) {
            return back()->with('error', 'Bukti penerimaan belum tersedia.');
        }

        $disk = Storage::disk('public');
        abort_unless($disk->exists($proposal->receipt_path), 404);

        return response()->download(
            $disk->path($proposal->receipt_path),
            'bukti-penerimaan-'.$proposal->proposal_no.'.pdf'
        );
    }

    public function preview(ProposalFile $file)
    {
        $this->authorizeAccess($file);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($file->file_path), 404, 'File tidak ditemukan.');

        return response()->file(
            $disk->path($file->file_path),
            [
                'Content-Type'        => $file->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.$file->original_name.'"',
            ]
        );
    }

    public function download(ProposalFile $file)
    {
        $this->authorizeAccess($file);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($file->file_path), 404, 'File tidak ditemukan.');

        return response()->download(
            $disk->path($file->file_path),
            $file->original_name,
            ['Content-Type' => $file->mime_type ?? 'application/octet-stream']
        );
    }

    private function authorizeAccess(ProposalFile $file): void
    {
        $user = Auth::user();

        // Yang boleh akses file proposal dari fitur "Manajemen File":
        // - super_admin
        // - user yang punya permission files.manage
        // - atau user files.view (karena item ini muncul di "Dokumen Dibagikan")
        abort_unless(
            $user->hasRole('super_admin') || $user->can('files.manage') || $user->can('files.view'),
            403
        );

        // Opsional (kalau kamu mau lebih ketat):
        // pastikan proposalnya ada
        abort_unless($file->proposal_id, 404);
    }
}
