<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class DocumentController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin') || $user->hasRole('tim_inti')) {
            $documents = Document::latest()->get();
        } else {
            // diasumsikan role lain = tim_bidang
            $documents = Document::where('bidang_id', $user->bidang_id)
                ->latest()
                ->get();
        }

        return view('documents.index', compact('documents'));
    }

    public function create(): View
    {
        return view('documents.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'title'       => 'required|string|max:255',
            'file'        => 'required|file|max:10240', // max 10MB contoh
            'description' => 'nullable|string',
        ]);

        $uploadedFile = $request->file('file');

        // Simpan file ke storage/app/documents
        $path = $uploadedFile->store('documents');

        // Tentukan bidang_id dokumen sesuai role
        $bidangId = null;
        $sieId    = null;

        if ($user->hasRole('tim_bidang')) {
            $bidangId   = $user->bidang_id;
            $sieId      = $user->sie_id;
        } elseif ($user->hasRole('tim_inti') || $user->hasRole('super_admin')) {
            $bidangId = null;
            $sieId = null;
        } else {
            abort(403, 'Anda tidak memiliki akses upload dokumen');
        }

        Document::create([
            'user_id'    => $user->id,
            'bidang_id'  => $bidangId,
            'sie_id'     => $sieId,
            'title'      => $request->title,
            'filename'   => $uploadedFile->getClientOriginalName(),
            'path'       => $path,
            'file_type'  => $uploadedFile->getClientMimeType(),
            'description'=> $request->description,
        ]);

        return redirect()->route('documents.index')->with('status', 'Dokumen berhasil diunggah.');
    }

    public function download(Document $document)
    {
        $user = Auth::user();

        // Authorization sederhana: siapa boleh download apa
        if ($user->hasRole('super_admin') || $user->hasRole('tim_inti')) {
            // boleh download semua dokumen
        } elseif ($user->hasRole('tim_bidang')) {
            // tim bidang hanya boleh download dokumen bidang sendiri
            if ($document->bidang_id !== $user->bidang_id) {
                abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
            }
        } else {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        if (! Storage::exists($document->path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::download($document->path, $document->filename);
    }

    public function destroy(Document $document)
    {
        $user = Auth::user();

        // Authorization
        if ($user->hasRole('super_admin')) {
            // Super Admin bisa menghapus semua
        } elseif ($user->hasRole('tim_inti')) {
            // Tim Inti hanya boleh menghapus dokumen inti (bidang_id null)
            if (!is_null($document->bidang_id)) {
                abort(403, 'Anda tidak boleh menghapus dokumen bidang');
            }
        } elseif ($user->hasRole('tim_bidang')) {
            // Tim Bidang hanya boleh menghapus dokumen bidang sendiri
            if ($document->bidang_id !== $user->bidang_id) {
                abort(403, 'Anda tidak boleh menghapus dokumen bidang lain.');
            }
        } else {
            abort(403);
        }

        // Menghapus file fisik
        if (Storage::exists($document->path)) {
            Storage::delete($document->path);
        }

        // Menghapus record DB agar tidak membekas
        $document->delete();

        return redirect()->route('documents.index')->with('status', 'Dokumen berhasil dihapus');
    }
}
