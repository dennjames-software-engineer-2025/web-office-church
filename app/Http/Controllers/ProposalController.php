<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Proposal;
use App\Models\ProposalFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    /* Form Upload Proposal */
    public function create(Program $program)
    {
        return view('proposals.create', compact('program'));
    }

    public function store(Request $request, Program $program)
    {
        $validated = $request->validate([
            'judul'      => 'required|string|max:255',
            'tujuan'     => 'required|string',
            'files'      => 'required|array|min:1',
            'files.*'    => 'file|mimes:pdf|max:51200',
        ]);

        $proposal = Proposal::create([
            'program_id' => $program->id,
            'user_id'    => auth()->id(),
            'judul'      => $validated['judul'],
            'tujuan'     => $validated['tujuan'],
            'status'     => 'pending',
        ]);

        foreach ($request->file('files') as $file) {
            $path = $file->store('proposal_files');

            ProposalFile::create([
                'proposal_id'   => $proposal->id,
                'original_name' => $file->getClientOriginalName(),
                'file_path'     => $path,
                'mime_type'     => $file->getMimeType(),
                'file_size'     => $file->getSize(),
            ]);
        }

        return redirect()
            ->route('programs.show', $program)
            ->with('status', 'Proposal berhasil diajukan dan menunggu persetujuan');
        }
}
