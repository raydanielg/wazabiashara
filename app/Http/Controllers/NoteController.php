<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::where('business_id', auth()->user()->business_id)
            ->orderByDesc('pinned')
            ->orderByDesc('id')
            ->get();

        return view('notebook.index', compact('notes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        $note = Note::create([
            'business_id' => auth()->user()->business_id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'color' => $request->color ?? 'gold',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Note imeongezwa!', 'note' => $note]);
        }

        return redirect()->route('notebook.index')->with('success', 'Note imeongezwa!');
    }

    public function update(Request $request, Note $note)
    {
        if ($note->business_id !== auth()->user()->business_id) abort(403);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'pinned' => 'nullable|boolean',
        ]);

        $note->update([
            'title' => $request->title,
            'content' => $request->content,
            'color' => $request->color ?? $note->color,
            'pinned' => $request->has('pinned') ? $request->boolean('pinned') : $note->pinned,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Note imesasishwa!', 'note' => $note]);
        }

        return redirect()->route('notebook.index')->with('success', 'Note imesasishwa!');
    }

    public function destroy(Note $note)
    {
        if ($note->business_id !== auth()->user()->business_id) abort(403);
        $note->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Note imefutwa.']);
        }

        return redirect()->route('notebook.index')->with('success', 'Note imefutwa.');
    }
}
