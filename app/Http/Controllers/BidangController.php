<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BidangController extends Controller
{
    public function index()
    {
        // tampilkan semua bidang (aktif dan nonaktif) agar admin bisa manage
        $bidangs = Bidang::orderBy('nama_bidang')->get();

        return view('bidangs.index', compact('bidangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bidang' => [
                'required',
                'string',
                'max:255',
                // unik untuk semua bidang (case-insensitive diselesaikan via lower() di DB idealnya)
                Rule::unique('bidangs', 'nama_bidang'),
            ],
        ]);

        Bidang::create([
            'nama_bidang' => $validated['nama_bidang'],
            'is_active'   => true, // default aktif
        ]);

        return redirect()
            ->route('bidangs.index')
            ->with('status', 'Bidang berhasil ditambahkan.');
    }

    public function destroy(Bidang $bidang)
    {
        // Hapus semua sie di dalam bidang
        foreach ($bidang->sies as $sie) {
            $sie->delete();
        }

        // Hapus bidang
        $bidang->delete();

        return redirect()
            ->route('bidangs.index')
            ->with('status', 'Bidang berhasil dihapus.');
    }

    public function update(Request $request, Bidang $bidang)
    {
        $validated = $request->validate([
            'nama_bidang' => [
                'required',
                'string',
                'max:255',
                // unik, tapi ignore bidang yang sedang diedit
                Rule::unique('bidangs', 'nama_bidang')->ignore($bidang->id),
            ],
        ]);

        $bidang->update([
            'nama_bidang' => $validated['nama_bidang'],
        ]);

        return redirect()
            ->route('bidangs.index')
            ->with('status', 'Bidang berhasil diperbarui.');
    }

    public function toggle(Bidang $bidang)
    {
        DB::transaction(function () use ($bidang) {
            // toggle bidang
            $bidang->is_active = ! $bidang->is_active;
            $bidang->save();

            // OPTIONAL (recommended): kalau bidang dinonaktifkan, semua sie di bawahnya ikut nonaktif
            if (! $bidang->is_active) {
                $bidang->sies()->update(['is_active' => false]);
            }
        });

        return redirect()
            ->route('bidangs.index')
            ->with('status', 'Status bidang berhasil diubah.');
    }
}
