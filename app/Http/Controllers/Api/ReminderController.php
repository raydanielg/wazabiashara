<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $businessId = $request->user()->business_id;

        $reminders = Reminder::where('business_id', $businessId)
            ->orderBy('remind_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reminders,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'nullable|string',
            'channel' => 'required|in:app,sms,whatsapp,email',
            'remind_at' => 'required|date',
        ]);

        $user = $request->user();

        $reminder = Reminder::create([
            'business_id' => $user->business_id,
            'user_id' => $user->id,
            'type' => $request->type,
            'title' => $request->title,
            'message' => $request->message,
            'channel' => $request->channel,
            'remind_at' => $request->remind_at,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kikumbusho kimeongezwa!',
            'reminder' => $reminder,
        ], 201);
    }

    public function destroy(Request $request, Reminder $reminder)
    {
        if ($reminder->business_id !== $request->user()->business_id) abort(403);
        $reminder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kikumbusho kimefutwa.',
        ]);
    }
}
