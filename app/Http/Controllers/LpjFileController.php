<?php

namespace App\Http\Controllers;

use App\Models\Lpj;
use App\Models\LpjFile;
use App\Models\Proposal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class LpjFileController extends Controller
{
    public function preview(Proposal $proposal, Lpj $lpj, LpjFile $file)
    {
        abort_unless((int)$lpj->proposal_id === (int)$proposal->id, 404);
        abort_unless((int)$file->lpj_id === (int)$lpj->id, 404);

        Gate::authorize('view', $lpj);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($file->file_path), 404, 'File tidak ditemukan.');

        return response()->file(
            $disk->path($file->file_path),
            [
                'Content-Type'        => $file->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.$file->original_name.'"',
            ]
        );
    }

    public function download(Proposal $proposal, Lpj $lpj, LpjFile $file)
    {
        abort_unless((int)$lpj->proposal_id === (int)$proposal->id, 404);
        abort_unless((int)$file->lpj_id === (int)$lpj->id, 404);

        Gate::authorize('downloadFile', $lpj);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($file->file_path), 404, 'File tidak ditemukan.');

        return response()->download(
            $disk->path($file->file_path),
            $file->original_name,
            ['Content-Type' => $file->mime_type ?? 'application/octet-stream']
        );
    }
}
