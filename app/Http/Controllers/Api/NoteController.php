<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $notes = Note::where('business_id', $request->user()->business_id)
            ->orderByDesc('pinned')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        $note = Note::create([
            'business_id' => $request->user()->business_id,
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->content,
            'color' => $request->color ?? 'gold',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note imeongezwa!',
            'data' => $note,
        ], 201);
    }

    public function update(Request $request, Note $note)
    {
        if ($note->business_id !== $request->user()->business_id) abort(403);

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

        return response()->json([
            'success' => true,
            'message' => 'Note imesasishwa!',
            'data' => $note,
        ]);
    }

    public function destroy(Request $request, Note $note)
    {
        if ($note->business_id !== $request->user()->business_id) abort(403);
        $note->delete();

        return response()->json([
            'success' => true,
            'message' => 'Note imefutwa.',
        ]);
    }
}
