<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BidangController extends Controller
{
    private array $kedudukanOptions = [
        'dpp_inti' => 'DPP Inti',
        'bgkp' => 'BGKP',
        'lingkungan' => 'Lingkungan',
        'sekretariat' => 'Sekretariat',
    ];

    public function index(Request $request)
    {
        $kedudukan = $request->query('kedudukan', 'dpp_inti');
        if (!array_key_exists($kedudukan, $this->kedudukanOptions)) {
            $kedudukan = 'dpp_inti';
        }

        $bidangs = Bidang::query()
            ->where('kedudukan', $kedudukan)
            ->withCount('sies')
            ->with([
                'ketua:id,name,bidang_id',
                'sies.users:id,name,email,sie_id',
            ])
            ->orderBy('nama_bidang')
            ->get();

        $kedudukanOptions = $this->kedudukanOptions;

        return view('bidangs.index', compact('bidangs', 'kedudukan', 'kedudukanOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kedudukan' => ['required', Rule::in(array_keys($this->kedudukanOptions))],
            'nama_bidang' => [
                'required',
                'string',
                'max:255',
                // unik PER kedudukan
                Rule::unique('bidangs', 'nama_bidang')->where(function ($q) use ($request) {
                    return $q->where('kedudukan', $request->input('kedudukan'));
                }),
            ],
        ]);

        Bidang::create([
            'kedudukan'   => $validated['kedudukan'],
            'nama_bidang' => $validated['nama_bidang'],
            'is_active'   => true,
        ]);

        return redirect()
            ->route('bidangs.index', ['kedudukan' => $validated['kedudukan']])
            ->with('status', 'Bidang berhasil ditambahkan.');
    }

    public function update(Request $request, Bidang $bidang)
    {
        $validated = $request->validate([
            'nama_bidang' => [
                'required',
                'string',
                'max:255',
                // unik PER kedudukan, ignore bidang ini
                Rule::unique('bidangs', 'nama_bidang')
                    ->where(fn ($q) => $q->where('kedudukan', $bidang->kedudukan))
                    ->ignore($bidang->id),
            ],
        ]);

        $bidang->update([
            'nama_bidang' => $validated['nama_bidang'],
        ]);

        return redirect()
            ->route('bidangs.index', ['kedudukan' => $bidang->kedudukan])
            ->with('status', 'Bidang berhasil diperbarui.');
    }

    public function destroy(Bidang $bidang)
    {
        $kedudukan = $bidang->kedudukan;
        $bidang->delete();

        return redirect()
            ->route('bidangs.index', ['kedudukan' => $kedudukan])
            ->with('status', 'Bidang berhasil dihapus.');
    }

    public function toggle(Bidang $bidang)
    {
        DB::transaction(function () use ($bidang) {
            $bidang->is_active = ! $bidang->is_active;
            $bidang->save();

            // kalau bidang nonaktif, sie ikut nonaktif
            if (! $bidang->is_active) {
                $bidang->sies()->update(['is_active' => false]);
            }
        });

        return redirect()
            ->route('bidangs.index', ['kedudukan' => $bidang->kedudukan])
            ->with('status', 'Status bidang berhasil diubah.');
    }
}
