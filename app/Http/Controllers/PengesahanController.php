<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PengesahanDokumen;
use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Fpdi;

class PengesahanController extends Controller
{
    // =====================================================================
    // LIST PENDING & APPROVED
    // =====================================================================
    public function index()
    {
        $pending = PengesahanDokumen::with('pengirim')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $accepted = PengesahanDokumen::with(['pengirim', 'pengesah'])
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('pengesahan.index', compact('pending', 'accepted'));
    }

    // =====================================================================
    // UPLOAD FORM (USER)
    // =====================================================================
    public function create()
    {
        return view('pengesahan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file'   => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'tujuan' => ['required', 'string', 'max:255'],
        ]);

        $file = $request->file('file');
        $path = $file->store('pengesahan');

        PengesahanDokumen::create([
            'user_id'       => Auth::id(),
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'mime_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
            'tujuan'        => $validated['tujuan'],
            'status'        => 'pending',
        ]);

        return redirect()->route('pengesahan.create')
            ->with('status', 'Dokumen berhasil dikirim.');
    }

    // =====================================================================
    // PREVIEW PDF
    // =====================================================================
    public function preview(PengesahanDokumen $doc)
    {
        if (!Storage::exists($doc->file_path)) {
            abort(404);
        }

        return response()->file(Storage::path($doc->file_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$doc->original_name.'"',
        ]);
    }

    // =====================================================================
    // ACCEPT
    // =====================================================================
    public function accept(PengesahanDokumen $doc)
    {
        $doc->update([
            'status'        => 'approved',
            'approved_by'   => Auth::id(),
            'approved_at'   => now(),
        ]);

        return redirect()->route('pengesahan.index')->with('status', 'Dokumen disetujui.');
    }

    // =====================================================================
    // REJECT
    // =====================================================================
    public function rejectForm(PengesahanDokumen $doc)
    {
        return view('pengesahan.reject', compact('doc'));
    }

    public function reject(Request $request, PengesahanDokumen $doc)
    {
        $request->validate([
            'alasan' => 'required|string|max:1000',
        ]);

        $doc->update([
            'status'          => 'rejected',
            'alasan_ditolak'  => $request->alasan,
            'approved_by'     => Auth::id(),
        ]);

        return redirect()->route('pengesahan.index')->with('status', 'Dokumen ditolak.');
    }

    // =====================================================================
    // SURAT
    // =====================================================================
    public function suratForm(PengesahanDokumen $doc)
    {
        return view('pengesahan.surat', compact('doc'));
    }

    public function suratStore(Request $request, PengesahanDokumen $doc)
    {
        $validated = $request->validate([
            'nama_pengesah' => 'required|string|max:255',
            'tujuan'        => 'required|string|max:255',
        ]);

        $pdf = Pdf::loadView('pengesahan.surat_pdf', [
            'doc'           => $doc,
            'nama_pengesah' => $validated['nama_pengesah'],
            'tujuan'        => $validated['tujuan'],
            'tanggal'       => now()->format('d M Y'),
        ]);

        $path = "pengesahan/surat-{$doc->id}.pdf";
        Storage::put($path, $pdf->output());

        $doc->update([ 'surat_path' => $path ]);

        return redirect()->route('pengesahan.index')->with('status', 'Surat pengesahan dibuat.');
    }

    // =====================================================================
    // WATERMARK
    // =====================================================================
    public function watermarkForm(PengesahanDokumen $doc)
    {
        return view('pengesahan.watermark', compact('doc'));
    }

    public function watermarkStore(Request $request, PengesahanDokumen $doc)
    {
        if (!Storage::exists($doc->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile(Storage::path($doc->file_path));

        for ($i = 1; $i <= $pageCount; $i++) {
            $template = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($template);

            $pdf->AddPage();
            $pdf->useTemplate($template);

            $pdf->SetFont('Helvetica', 'B', 40);
            $pdf->SetTextColor(150, 150, 150);
            if (method_exists($pdf, 'SetAlpha')) {
                $pdf->SetAlpha(0.2);
            }

            $pdf->SetXY(0, $size['height'] / 2);
            $pdf->Cell($size['width'], 10, 'DISAHKAN', 0, 0, 'C');
        }

        $output = "pengesahan/watermark-{$doc->id}.pdf";
        Storage::put($output, $pdf->Output('S'));

        $doc->update(['watermarked_path' => $output]);

        return redirect()->route('pengesahan.index')->with('status', 'Watermark berhasil.');
    }

    // =====================================================================
    // DELETE
    // =====================================================================
    public function destroy(PengesahanDokumen $doc)
    {
        Storage::delete([
            $doc->file_path,
            $doc->surat_path,
            $doc->watermarked_path,
        ]);

        $doc->delete();

        return redirect()->route('pengesahan.index')->with('status', 'Dokumen dihapus.');
    }

    // ======================================================================
    // USER — FORM UPLOAD
    // ======================================================================
    public function userCreate()
    {
        return view('pengesahan.user-create');
    }


    // ======================================================================
    // USER — STORE UPLOAD
    // ======================================================================
    public function userStore(Request $request)
    {
        $validated = $request->validate([
            'file'   => ['required', 'file', 'mimes:pdf', 'max:20480'], // 20 MB
            'tujuan' => ['required', 'string', 'max:255'],
        ]);

        $file = $request->file('file');
        $path = $file->store('pengesahan');

        PengesahanDokumen::create([
            'user_id'       => Auth::id(),
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'mime_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
            'tujuan'        => $validated['tujuan'],
            'status'        => 'pending',
        ]);

        return redirect()
            ->route('pengesahan.userHistory')
            ->with('status', 'Dokumen berhasil dikirim untuk pengesahan.');
    }


    // ======================================================================
    // USER — RIWAYAT
    // ======================================================================
    public function userHistory()
    {
        $docs = PengesahanDokumen::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('pengesahan.user-history', compact('docs'));
    }


    // ======================================================================
    // USER — HAPUS PENGAJUAN
    // ======================================================================
    public function userDestroy(PengesahanDokumen $doc)
    {
        // Pastikan user hanya bisa hapus miliknya
        if ($doc->user_id !== Auth::id()) {
            abort(403, 'Anda tidak boleh menghapus dokumen ini.');
        }

        Storage::delete([
            $doc->file_path,
            $doc->surat_path,
            $doc->watermark_path,
        ]);

        $doc->delete();

        return redirect()
            ->route('pengesahan.userHistory')
            ->with('status', 'Pengajuan berhasil dihapus.');
    }
}
