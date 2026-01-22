<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Folder;
use App\Models\Template;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    /**
     * INDEX — Menampilkan daftar Template Inti & Template Bidang
     */
    public function index(): View
    {
        $user = Auth::user();

        $isInti = $user->hasAnyRole(['super_admin','ketua','wakil_ketua','sekretaris','bendahara']);
        $bidangs = Bidang::orderBy('nama_bidang')->get();

        // TEMPLATE INTI: bidang_id NULL
        $templatesInti = Template::with(['uploader', 'bidangs'])
            ->whereNull('bidang_id')
            ->when(! $isInti, function ($q) use ($user) {
                // non-inti hanya lihat inti yang dishare ke bidangnya
                $q->whereHas('bidangs', fn($b) => $b->where('bidang_id', $user->bidang_id));
            })
            ->latest()
            ->get();

        // TEMPLATE BIDANG: bidang_id NOT NULL
        $templatesBidang = Template::with(['uploader', 'bidang'])
            ->whereNotNull('bidang_id')
            ->when(! $isInti, function ($q) use ($user) {
                // non-inti hanya lihat template bidang sendiri
                $q->where('bidang_id', $user->bidang_id);
            })
            ->latest()
            ->get();

        $folders = $user->can('files.manage')
            ? Folder::where('created_by', $user->id)->orderBy('name')->get()
            : collect();

        return view('templates.index', compact('templatesInti','templatesBidang','bidangs','folders'));
    }

    /**
     * FORM CREATE TEMPLATE
     */
    public function create(): View
    {
        $bidangs = Bidang::orderBy('nama_bidang')->get();
        return view('templates.create', compact('bidangs'));
    }

    /**
     * STORE TEMPLATE
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $isInti = $user->hasAnyRole(['super_admin','ketua','wakil_ketua','sekretaris','bendahara']);
        $isNonInti = $user->hasAnyRole(['ketua_bidang','ketua_sie','anggota_komunitas','ketua_lingkungan','wakil_ketua_lingkungan']);

        $validated = $request->validate([
            'title' => ['required','string','max:255'],
            'file'  => ['required','file','max:10240','mimes:pdf,doc,docx,xls,xlsx,ppt,pptx'],
            'share_bidangs' => ['nullable','array'],
            'share_bidangs.*' => ['integer','exists:bidangs,id'],
        ]);

        $file = $request->file('file');

        // simpan ke disk public biar aman untuk preview (nanti langkah 6)
        $path = $file->store('templates', 'public');

        $template = Template::create([
            'title'         => $validated['title'],
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
            'mime_type'     => $file->getClientMimeType(),
            'file_type'     => $file->getClientOriginalExtension(),
            'size'          => $file->getSize(),
            'uploaded_by'   => $user->id,

            // inti => null, non-inti => bidang user
            'bidang_id'     => $isInti ? null : $user->bidang_id,
        ]);

        // hanya inti yang boleh share
        if ($isInti) {
            $template->bidangs()->sync($request->input('share_bidangs', []));
        }

        return redirect()->route('templates.index')->with('status', 'Template berhasil diupload');
    }

    /**
     * DOWNLOAD TEMPLATE
     */
    public function download(Template $template)
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($template->path)) {
            return back()->with('error', 'File template tidak ditemukan');
        }

        return response()->download(
            $disk->path($template->path),
            $template->original_name,
            ['Content-Type' => $template->mime_type ?? 'application/octet-stream']
        );
    }

    /**
     * STREAM PDF
     */
    public function stream(Template $template)
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($template->path)) {
            abort(404, 'File tidak ditemukan.');
        }

        if (strtolower($template->file_type) !== 'pdf') {
            return redirect()->route('templates.view', $template);
        }

        return response()->file($disk->path($template->path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$template->original_name.'"',
        ]);
    }

    /**
     * SHOW TEMPLATE DETAIL
     */
    public function show(Template $template)
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($template->path)) {
            return back()->with('error', 'File tidak ditemukan');
        }

        $isPdf = strtolower($template->file_type) === 'pdf';

        return view('templates.show', compact('template', 'isPdf'));
    }

    public function view(Template $template)
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($template->path)) {
            abort(404);
        }

        $url = asset('storage/'.$template->path); // publik
        return redirect("https://docs.google.com/gview?url={$url}&embedded=true");
    }

    /**
     * DESTROY TEMPLATE
     */
    public function destroy(Template $template)
    {
        $user = Auth::user();

        /* ===========================================================
         * DELETE TEMPLATE BIDANG
         * ===========================================================
         * Super Admin → boleh hapus
         * Tim Bidang → boleh hapus jika dia yang upload
         * Tim Inti → TIDAK BOLEH
         */
        if (!is_null($template->bidang_id)) {

            if ($user->hasRole('super_admin')) {
                // allowed
            }
            elseif ($user->hasRole('tim_bidang') && $template->uploaded_by === $user->id) {
                // allowed
            }
            else {
                abort(403, 'Anda tidak memiliki akses untuk menghapus template bidang ini.');
            }
        }

        /* ===========================================================
         * DELETE TEMPLATE INTI
         * ===========================================================
         * Super Admin & Tim Inti → boleh hapus
         * Tim Bidang → tidak boleh
         */
        else {
            if (! ($user->hasRole('super_admin') || $user->hasRole('tim_inti'))) {
                abort(403, 'Anda tidak memiliki akses untuk menghapus template inti ini.');
            }
        }

        // Hapus file fisik
        if (Storage::exists($template->path)) {
            Storage::delete($template->path);
        }

        // Soft Delete DB
        $template->delete();

        return redirect()
            ->route('templates.index')
            ->with('status', 'Template berhasil dihapus');
    }
}
