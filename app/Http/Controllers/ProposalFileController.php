<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\ProposalFile;
use Illuminate\Support\Facades\Storage;

class ProposalFileController extends Controller
{
    public function preview(Proposal $proposal, ProposalFile $file)
    {
        abort_unless($file->proposal_id === $proposal->id, 404);

        $this->authorize('viewFile', $proposal);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($file->file_path), 404);

        return response()->file($disk->path($file->file_path), [
            'Content-Type' => $file->mime_type ?? 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$file->original_name.'"',
        ]);
    }

    public function download(Proposal $proposal, ProposalFile $file)
    {
        abort_unless($file->proposal_id === $proposal->id, 404);

        $this->authorize('downloadFile', $proposal);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($file->file_path), 404);

        return response()->download($disk->path($file->file_path), $file->original_name);
    }
}