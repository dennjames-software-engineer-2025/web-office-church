<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:files.manage']);
    }

    public function index()
    {
        $folders = Folder::where('created_by', Auth::id())
            ->latest()
            ->get();

        return view('files.folders.index', compact('folders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Folder::create([
            'name' => $data['name'],
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('folders.index')->with('status', 'Folder berhasil dibuat.');
    }

    public function show(Folder $folder)
    {
        abort_unless($folder->created_by === Auth::id() || Auth::user()->hasRole('super_admin'), 403);

        $folder->load([
            'items' => function ($q) {
                $q->latest()->with('source'); // IMPORTANT: load morph source
            }
        ]);

        $bidangs = Bidang::orderBy('nama_bidang')->get();

        return view('files.folders.show', compact('folder', 'bidangs'));
    }

    public function destroy(Folder $folder)
    {
        $user = Auth::user();

        abort_unless($folder->created_by === $user->id || $user->hasRole('super_admin'), 403);

        $folder->delete();

        return redirect()->route('folders.index')->with('status', 'Folder dihapus.');
    }
}
