<?php

namespace App\Http\Controllers;

use App\Models\MeetingMinute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MeetingMinuteController extends Controller
{
    public function index(Request $request): View
    {
        $minutes = MeetingMinute::with('creator')
            ->latest('meeting_at')
            ->get();

        return view('minutes.index', compact('minutes'));
    }

    public function create(): View
    {
        abort_unless(Auth::user()->canManageMinutes(), 403);

        return view('minutes.create');
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->canManageMinutes(), 403);

        $data = $request->validate([
            'title'      => ['required','string','max:255'],
            'meeting_at' => ['required','date'],
            'location'   => ['nullable','string','max:255'],
            'agenda'     => ['nullable','string'],
            'content'    => ['required','string'],
            'status'     => ['required','in:draft,published'],
        ]);

        $data['created_by'] = Auth::id();
        $data['kedudukan']  = Auth::user()->kedudukan; // opsional (kalau ada)

        MeetingMinute::create($data);

        return redirect()->route('minutes.index')->with('status', 'Notulensi berhasil dibuat.');
    }

    public function show(MeetingMinute $minute): View
    {
        $minute->load('creator');
        return view('minutes.show', compact('minute'));
    }

    public function edit(MeetingMinute $minute): View
    {
        abort_unless(Auth::user()->canManageMinutes(), 403);

        return view('minutes.edit', compact('minute'));
    }

    public function update(Request $request, MeetingMinute $minute)
    {
        abort_unless(Auth::user()->canManageMinutes(), 403);

        $data = $request->validate([
            'title'      => ['required','string','max:255'],
            'meeting_at' => ['required','date'],
            'location'   => ['nullable','string','max:255'],
            'agenda'     => ['nullable','string'],
            'content'    => ['required','string'],
            'status'     => ['required','in:draft,published'],
        ]);

        $minute->update($data);

        return redirect()->route('minutes.show', $minute)->with('status', 'Notulensi berhasil diperbarui.');
    }

    public function destroy(MeetingMinute $minute)
    {
        abort_unless(Auth::user()->canManageMinutes(), 403);

        $minute->delete();

        return redirect()->route('minutes.index')->with('status', 'Notulensi berhasil dihapus.');
    }
}