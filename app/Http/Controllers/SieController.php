<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Sie;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SieController extends Controller
{
    public function index(Bidang $bidang)
    {
        // list sie untuk bidang ini (aktif dan nonaktif)
        $sies = $bidang->sies()->orderBy('nama_sie')->get();

        return view('sies.index', compact('bidang', 'sies'));
    }

    public function destroy(Sie $sie)
    {
        $sie->delete();

        return redirect()
            ->route('sies.index', $sie->bidang_id)
            ->with('status', 'Sie berhasil dihapus.');
    }

    public function store(Request $request, Bidang $bidang)
    {
        $validated = $request->validate([
            'nama_sie' => [
                'required',
                'string',
                'max:255',
                /**
                 * Unik per bidang:
                 * - boleh ada nama sie sama di bidang lain
                 * - tapi tidak boleh duplikat di bidang yang sama
                 */
                Rule::unique('sies', 'nama_sie')->where(function ($q) use ($bidang) {
                    return $q->where('bidang_id', $bidang->id);
                }),
            ],
        ]);

        // optional guard: kalau bidang nonaktif, jangan boleh tambah sie
        if (! $bidang->is_active) {
            return back()->with('error', 'Bidang ini nonaktif. Aktifkan bidang sebelum menambah Sie.');
        }

        Sie::create([
            'bidang_id' => $bidang->id,
            'nama_sie'  => $validated['nama_sie'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('sies.index', $bidang)
            ->with('status', 'Sie berhasil ditambahkan.');
    }

    public function update(Request $request, Sie $sie)
    {
        $validated = $request->validate([
            'nama_sie' => [
                'required',
                'string',
                'max:255',
                // unik per bidang, ignore sie yang sedang diedit
                Rule::unique('sies', 'nama_sie')
                    ->where(fn ($q) => $q->where('bidang_id', $sie->bidang_id))
                    ->ignore($sie->id),
            ],
        ]);

        $sie->update([
            'nama_sie' => $validated['nama_sie'],
        ]);

        return redirect()
            ->route('sies.index', $sie->bidang_id)
            ->with('status', 'Sie berhasil diperbarui.');
    }

    public function toggle(Sie $sie)
    {
        $sie->is_active = ! $sie->is_active;
        $sie->save();

        return redirect()
            ->route('sies.index', $sie->bidang_id)
            ->with('status', 'Status sie berhasil diubah.');
    }
}
