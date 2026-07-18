<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $email = strtolower(trim($request->input('email', '')));

        if ($email === '') {
            return response()->json(['success' => false, 'message' => 'Tafadhali andika barua pepe yako.']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => false, 'message' => 'Barua pepe uliyoandika si sahihi.']);
        }

        if (strlen($email) > 190) {
            return response()->json(['success' => false, 'message' => 'Barua pepe ni ndefu kupita kiasi.']);
        }

        $list = [];
        if (Storage::disk('local')->exists('newsletter/subscribers.json')) {
            $list = json_decode(Storage::disk('local')->get('newsletter/subscribers.json'), true) ?: [];
        }

        foreach ($list as $row) {
            if (($row['email'] ?? '') === $email) {
                return response()->json(['success' => true, 'message' => 'Tayari umejiunga na jarida letu. Asante!']);
            }
        }

        $list[] = [
            'email'      => $email,
            'ip'         => $request->ip(),
            'created_at' => now()->toDateTimeString(),
        ];

        Storage::disk('local')->put('newsletter/subscribers.json', json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return response()->json(['success' => true, 'message' => 'Umejiunga kikamilifu! Karibu Wazabiashara.']);
    }
}
