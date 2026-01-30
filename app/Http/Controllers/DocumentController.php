<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Folder;
use App\Models\Document;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class DocumentController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $isInti = $user->hasAnyRole(['super_admin','ketua','wakil_ketua','sekretaris','bendahara']);
        $bidangs = Bidang::orderBy('nama_bidang')->get();

        $documents = Document::with(['user','bidang','sie','sharedBidangs'])
            ->when(! $isInti, function ($q) use ($user) {
                $q->where(function ($qq) use ($user) {
                    $qq->where('bidang_id', $user->bidang_id)
                        ->orWhere(function ($q2) use ($user) {
                            $q2->whereNull('bidang_id')
                                ->whereHas('sharedBidangs', function ($b) use ($user) {
                                    $b->where('bidangs.id', $user->bidang_id);
                                });
                        });
                });
            })
            ->latest()
            ->get();

        $folders = Auth::user()->can('files.manage')
            ? Folder::where('created_by', Auth::id())->orderBy('name')->get()
            : collect();

        return view('documents.index', compact('documents','bidangs','isInti','folders'));
    }

    public function create(): View
    {
        $user = Auth::user();
        $isInti = $user->hasAnyRole(['super_admin','ketua','wakil_ketua','sekretaris','bendahara']);
        $bidangs = Bidang::orderBy('nama_bidang')->get();

        return view('documents.create', compact('isInti','bidangs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $isInti = $user->hasAnyRole(['super_admin','ketua','wakil_ketua','sekretaris','bendahara']);
        $isNonInti = $user->hasAnyRole(['ketua_bidang','ketua_sie','anggota_komunitas','ketua_lingkungan','wakil_ketua_lingkungan']);

        $validated = $request->validate([
            'title'       => ['required','string','max:255'],
            'file'        => ['required','file','max:10240','mimes:pdf,doc,docx,xls,xlsx,ppt,pptx'],
            'description' => ['nullable','string'],
            'share_bidangs' => ['nullable','array'],
            'share_bidangs.*' => ['integer','exists:bidangs,id'],
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $bidangId = null;
        $sieId = null;

        if ($isNonInti) {
            $bidangId = $user->bidang_id;
            $sieId    = $user->sie_id;
        } elseif ($isInti) {
            $bidangId = null;
            $sieId = null;
        } else {
            abort(403, 'Anda tidak memiliki akses upload dokumen');
        }

        $doc = Document::create([
            'user_id'     => $user->id,
            'bidang_id'   => $bidangId,
            'sie_id'      => $sieId,
            'title'       => $validated['title'],
            'filename'    => $file->getClientOriginalName(),
            'path'        => $path,
            'file_type'   => $file->getClientMimeType(),
            'description' => $validated['description'] ?? null,
        ]);

        if ($isInti) {
            $doc->sharedBidangs()->sync($request->input('share_bidangs', []));
        }

        return redirect()->route('documents.index')->with('status', 'Dokumen berhasil diunggah.');
    }

    /**
     * ✅ PREVIEW: inline (konsisten dengan Template/ProposalFile)
     */
    public function preview(Document $document)
    {
        $this->ensureCanAccess($document);

        $disk = Storage::disk('public');
        abort_unless($document->path && $disk->exists($document->path), 404, 'File tidak ditemukan.');

        $absolutePath = $disk->path($document->path);

        // FIX: jangan pakai $disk->mimeType() (bisa error pada beberapa driver)
        $mime = $document->file_type ?: (File::exists($absolutePath) ? (File::mimeType($absolutePath) ?: 'application/octet-stream') : 'application/octet-stream');
        $name = $document->filename ?: ($document->title ? ($document->title) : 'document');

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$name.'"',
        ]);
    }

    public function download(Document $document)
    {
        $this->ensureCanAccess($document);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($document->path), 404, 'File tidak ditemukan.');

        return response()->download(
            $disk->path($document->path),
            $document->filename,
            ['Content-Type' => $document->file_type ?? 'application/octet-stream']
        );
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        // ❌ JANGAN hapus file fisik kalau konsepnya arsip aman
        // $disk = Storage::disk('public');
        // if ($disk->exists($document->path)) {
        //     $disk->delete($document->path);
        // }

        $document->delete(); // soft delete

        return redirect()->route('documents.index')->with('status', 'Dokumen berhasil dihapus');
    }

    /**
     * Helper: aturan akses dokumen (inti / non-inti)
     */
    private function ensureCanAccess(Document $document): void
    {
        $user = Auth::user();
        $isInti = $user->hasAnyRole(['super_admin','ketua','wakil_ketua','sekretaris','bendahara']);

        if ($isInti) return;

        $allowed =
            ($document->bidang_id === $user->bidang_id)
            || (
                is_null($document->bidang_id)
                && $document->sharedBidangs()->where('bidang_id', $user->bidang_id)->exists()
            );

        abort_unless($allowed, 403);
    }
}
