<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Folder;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TemplateController extends Controller
{
    /**
     * Definisi DPP Harian berdasarkan RolePermissionSeeder kamu:
     * ketua, wakil_ketua, sekretaris, bendahara
     */
    private function dppHarianRoles(): array
    {
        return ['ketua', 'wakil_ketua', 'sekretaris', 'bendahara'];
    }

    private function isDppHarian($user): bool
    {
        return $user->hasAnyRole($this->dppHarianRoles());
    }

    /**
     * INDEX — 1 tabel saja (tanpa Tim Inti/Bidang, tanpa shared pivot)
     *
     * RULE FINAL (sesuai brief terbaru kamu):
     * 1) DPP Harian upload -> hanya DPP Harian yang bisa lihat sesama template DPP Harian
     * 2) Ketua Bidang & Ketua Sie upload -> template mereka MUNCUL di halaman DPP Harian
     * 3) Ketua Bidang -> boleh lihat:
     *      - template miliknya sendiri
     *      - template bidangnya yang share_to_bidang = true (1 bidang saja)
     * 4) Ketua Sie -> hanya lihat template miliknya sendiri (private)
     * 5) Tidak boleh lintas bidang untuk Ketua Bidang
     * 6) Super Admin -> lihat semua
     */
    public function index(): View
    {
        $user = Auth::user();

        $query = Template::with(['uploader', 'bidang'])
            ->latest();

        // SUPER ADMIN: lihat semua
        if ($user->hasRole('super_admin')) {
            // no filter
        }
        // DPP HARIAN: lihat template DPP Harian + semua template dari Ketua Bidang & Ketua Sie
        elseif ($this->isDppHarian($user)) {
            $dppRoles = $this->dppHarianRoles();

            $query->where(function ($q) use ($dppRoles) {
                $q->whereHas('uploader.roles', function ($r) use ($dppRoles) {
                    $r->whereIn('name', $dppRoles);
                })
                ->orWhereHas('uploader.roles', function ($r) {
                    $r->whereIn('name', ['ketua_bidang', 'ketua_sie']);
                });
            });
        }
        // KETUA SIE: hanya miliknya sendiri
        elseif ($user->hasRole('ketua_sie')) {
            $query->where('uploaded_by', $user->id);
        }
        // KETUA BIDANG: miliknya sendiri + template bidang yang dishare (1 bidang)
        elseif ($user->hasRole('ketua_bidang')) {
            $query->where(function ($q) use ($user) {
                $q->where('uploaded_by', $user->id)
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('bidang_id', $user->bidang_id)
                         ->where('share_to_bidang', true);
                  });
            });
        }
        // ROLE LAIN (sekretariat / ketua_lingkungan / dll): aman -> hanya miliknya sendiri
        else {
            $query->where('uploaded_by', $user->id);
        }

        $templates = $query->paginate(10)->withQueryString();

        $folders = $user->can('files.manage')
            ? Folder::where('created_by', $user->id)->orderBy('name')->get()
            : collect();

        return view('templates.index', compact('templates', 'folders'));
    }

    /**
     * FORM CREATE TEMPLATE
     */
    public function create(): View
    {
        // tidak wajib, tapi biarkan supaya view tetap aman jika kamu butuh bidang list
        $bidangs = Bidang::orderBy('nama_bidang')->get();
        return view('templates.create', compact('bidangs'));
    }

    /**
     * STORE TEMPLATE — aturan final:
     * - DPP Harian upload -> private DPP Harian (share_to_bidang = false, bidang_id = null)
     * - Ketua Bidang upload -> share_to_bidang = true, bidang_id = user->bidang_id
     * - Ketua Sie upload -> share_to_bidang = false, bidang_id = user->bidang_id (private)
     * - Role lain -> private miliknya sendiri
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file'  => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx'],
        ]);

        $file = $request->file('file');
        $path = $file->store('templates', 'public');

        $isDppHarian = $this->isDppHarian($user);

        // default private
        $shareToBidang = false;
        $bidangId = $isDppHarian ? null : $user->bidang_id;

        // ketua bidang: dishare ke bidang
        if ($user->hasRole('ketua_bidang')) {
            $shareToBidang = true;
        }

        Template::create([
            'title'           => $validated['title'],
            'original_name'   => $file->getClientOriginalName(),
            'path'            => $path,
            'mime_type'       => $file->getClientMimeType(),
            'file_type'       => $file->getClientOriginalExtension(),
            'size'            => $file->getSize(),
            'uploaded_by'     => $user->id,
            'bidang_id'       => $bidangId,
            'share_to_bidang' => $shareToBidang,
        ]);

        return redirect()->route('templates.index')->with('status', 'Template berhasil diupload');
    }

    public function download(Template $template)
    {
        $this->authorizeTemplateAccess($template);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($template->path), 404, 'File template tidak ditemukan');

        return response()->download(
            $disk->path($template->path),
            $template->original_name,
            ['Content-Type' => $template->mime_type ?? 'application/octet-stream']
        );
    }

    public function stream(Template $template)
    {
        $this->authorizeTemplateAccess($template);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($template->path), 404, 'File tidak ditemukan.');

        if (strtolower($template->file_type) !== 'pdf') {
            return redirect()->route('templates.view', $template);
        }

        return response()->file($disk->path($template->path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$template->original_name.'"',
        ]);
    }

    public function show(Template $template)
    {
        $this->authorizeTemplateAccess($template);

        $disk = Storage::disk('public');
        if (! $disk->exists($template->path)) {
            return back()->with('error', 'File tidak ditemukan');
        }

        $isPdf = strtolower($template->file_type) === 'pdf';

        return view('templates.show', compact('template', 'isPdf'));
    }

    public function view(Template $template)
    {
        $this->authorizeTemplateAccess($template);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($template->path), 404);

        $url = asset('storage/'.$template->path);
        return redirect("https://docs.google.com/gview?url={$url}&embedded=true");
    }

    /**
     * DESTROY — hanya:
     * - super_admin
     * - sekretaris (karena hanya role ini yang punya files.manage di seeder kamu)
     *
     * Catatan: kamu minta Sekretaris 1/2 saja, tapi di Spatie role kamu cuma "sekretaris".
     * Jadi logika yang paling konsisten adalah: siapa pun yang punya permission files.manage boleh hapus.
     */
    public function destroy(Template $template)
    {
        $user = Auth::user();

        $canDelete = $user->hasRole('super_admin') || $user->can('files.manage');
        abort_unless($canDelete, 403, 'Anda tidak memiliki akses untuk menghapus template ini.');

        $disk = Storage::disk('public');
        if ($disk->exists($template->path)) {
            $disk->delete($template->path);
        }

        $template->delete();

        return redirect()->route('templates.index')->with('status', 'Template berhasil dihapus');
    }

    /**
     * Akses kontrol untuk Preview/Download/Show/View sesuai rule INDEX
     */
    private function authorizeTemplateAccess(Template $template): void
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) return;

        // pastikan roles uploader tersedia (untuk cek DPP Harian)
        $template->loadMissing('uploader.roles');

        // DPP Harian boleh lihat template dari:
        // - DPP Harian (ketua/wakil_ketua/sekretaris/bendahara)
        // - ketua_bidang
        // - ketua_sie
        if ($this->isDppHarian($user)) {
            $allowedRoles = array_merge($this->dppHarianRoles(), ['ketua_bidang', 'ketua_sie']);

            $allowed = $template->uploader
                && $template->uploader->roles
                && $template->uploader->roles->pluck('name')->intersect($allowedRoles)->isNotEmpty();

            abort_unless($allowed, 403, 'Anda tidak memiliki akses untuk melihat template ini.');
            return;
        }

        // Ketua Sie: hanya miliknya sendiri
        if ($user->hasRole('ketua_sie')) {
            abort_unless((int)$template->uploaded_by === (int)$user->id, 403, 'Anda tidak memiliki akses.');
            return;
        }

        // Ketua Bidang: miliknya sendiri atau template bidang yang dishare
        if ($user->hasRole('ketua_bidang')) {
            $allowed =
                (int)$template->uploaded_by === (int)$user->id
                || (
                    (int)$template->bidang_id === (int)$user->bidang_id
                    && (bool)$template->share_to_bidang === true
                );

            abort_unless($allowed, 403, 'Anda tidak memiliki akses.');
            return;
        }

        // Role lain: default aman -> hanya miliknya sendiri
        abort_unless((int)$template->uploaded_by === (int)$user->id, 403, 'Anda tidak memiliki akses.');
    }
}
