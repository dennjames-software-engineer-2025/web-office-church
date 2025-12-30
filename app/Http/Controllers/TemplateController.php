<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TemplateController extends Controller
{
    /**
     * INDEX — Menampilkan daftar Template Inti & Template Bidang
     */
    public function index(): View
    {
        $user = Auth::user();

        // For share checkbox
        $bidangs = Bidang::orderBy('nama_bidang')->get();

        /* ===========================================================
         * TEMPLATE INTI (bidang_id = NULL)
         * ===========================================================
         * Super Admin / Tim Inti → bisa melihat semua template inti
         * Tim Bidang → hanya template inti yang dibagikan ke bidangnya
         */
        $templatesInti = Template::with(['uploader', 'bidangs'])
            ->whereNull('bidang_id')
            ->when($user->hasRole('tim_bidang'), function ($q) use ($user) {
                $q->whereHas('bidangs', fn($b) => $b->where('bidang_id', $user->bidang_id));
            })
            ->latest()
            ->get();


        /* ===========================================================
         * TEMPLATE BIDANG (bidang_id != NULL)
         * ===========================================================
         * Super Admin → melihat semua
         * Tim Inti → melihat semua
         * Tim Bidang → hanya bidang dia sendiri
         */
        $templatesBidang = Template::with(['uploader', 'bidang'])
            ->whereNotNull('bidang_id')
            ->when($user->hasRole('tim_bidang'), function ($q) use ($user) {
                $q->where('bidang_id', $user->bidang_id);
            })
            ->latest()
            ->get();

        return view('templates.index', compact('templatesInti', 'templatesBidang', 'bidangs'));
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

        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'file'          => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx'],
            'share_bidangs' => ['nullable', 'array'],
            'share_bidangs.*' => ['integer', 'exists:bidangs,id'],
        ]);

        $file = $request->file('file');
        $path = $file->store('templates');

        /* ===========================================================
         * TEMPLATE BIDANG (Tim Bidang)
         * ===========================================================
         */
        if ($user->hasRole('tim_bidang')) {

            $template = Template::create([
                'title'         => $validated['title'],
                'original_name' => $file->getClientOriginalName(),
                'path'          => $path,
                'mime_type'     => $file->getClientMimeType(),
                'file_type'     => $file->getClientOriginalExtension(),
                'size'          => $file->getSize(),
                'uploaded_by'   => $user->id,
                'bidang_id'     => $user->bidang_id, // PRIVATE to bidang
            ]);

            return redirect()
                ->route('templates.index')
                ->with('status', 'Template Bidang berhasil diupload');
        }

        /* ===========================================================
         * TEMPLATE INTI (Super Admin & Tim Inti)
         * ===========================================================
         * bidang_id = NULL
         */
        $template = Template::create([
            'title'         => $validated['title'],
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
            'mime_type'     => $file->getClientMimeType(),
            'file_type'     => $file->getClientOriginalExtension(),
            'size'          => $file->getSize(),
            'uploaded_by'   => $user->id,
            'bidang_id'     => null,
        ]);

        // Share ke beberapa bidang
        $shareBidangs = $request->input('share_bidangs', []);
        $template->bidangs()->sync($shareBidangs);

        return redirect()
            ->route('templates.index')
            ->with('status', 'Template Inti berhasil diupload');
    }

    /**
     * DOWNLOAD TEMPLATE
     */
    public function download(Template $template)
    {
        if (! Storage::exists($template->path)) {
            return back()->with('error', 'File template tidak ditemukan');
        }

        return Storage::download($template->path, $template->original_name);
    }

    /**
     * STREAM PDF
     */
    public function stream(Template $template)
    {
        if (!Storage::exists($template->path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Hanya PDF untuk inline PDF viewer
        if (strtolower($template->file_type) !== 'pdf') {
            return redirect()->route('templates.view', $template);
        }

        return response()->file(Storage::path($template->path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$template->original_name.'"',
        ]);
    }

    /**
     * SHOW TEMPLATE DETAIL
     */
    public function show(Template $template)
    {
        if (! Storage::exists($template->path)) {
            return back()->with('error', 'File tidak ditemukan');
        }

        $isPdf = strtolower($template->file_type) === 'pdf';

        return view('templates.show', compact('template', 'isPdf'));
    }

    public function view(Template $template)
    {
        if (!Storage::exists($template->path)) {
            abort(404);
        }

        $url = url(Storage::url($template->path));

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
