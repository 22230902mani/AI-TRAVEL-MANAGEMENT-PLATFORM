<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function __construct(private ChatbotService $chatbot) {}

    public function index()
    {
        $history = auth()->user()->chatMessages()
            ->whereNull('itinerary_id')
            ->latest()->limit(20)->get()->reverse()->values();

        return view('chatbot.index', compact('history'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:1000',
            'session_id' => 'nullable|string',
        ]);

        $sessionId = $request->session_id ?? Str::uuid()->toString();

        $response = $this->chatbot->respond(
            $request->message,
            auth()->id(),
            $sessionId
        );

        return response()->json([
            'success'    => true,
            'session_id' => $sessionId,
            'response'   => $response,
        ]);
    }
}
