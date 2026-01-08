<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ToolsController extends Controller
{
    public function signature()
    {
        return view('tools.signature');
    }

    public function saveSignature(Request $request)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        // Data Base64 dari browser
        $base64Image = $request->signature;

        // Hilangkan prefix data:image/png;base64,
        $cleanImage = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);

        // Decode base64 ke binary
        $imageData = base64_decode($cleanImage);

        // Nama file unik
        $filename = 'signature_' . now()->timestamp . '.png';

        // Simpan ke storage/app/signatures
        Storage::put('signatures/' . $filename, $imageData);

        return back()->with('status', 'Tanda tangan berhasil disimpan!');
    }
}
