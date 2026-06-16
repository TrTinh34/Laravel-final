<?php

namespace App\Http\Controllers;

use App\Ai\Agents\SupportBot;
use App\Models\AgentConversation;
use App\Models\AgentConversationMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class ChatController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'message' => 'required',
        ]);

        try {

            $conversation = AgentConversation::firstOrCreate([
                'user_id' => Auth::id(),
                'title' => 'New Conversation',
            ]);

            // lưu user
            AgentConversationMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'user',
                'user_id' => Auth::id(),
                'content' => $request->message,
                'agent' => 'SupportBot',
            ]);


            $agent = app(SupportBot::class);

            $response = $agent->prompt($request->message);

            // lưu assistant
            AgentConversationMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $response->text,
                'user_id' => Auth::id(),
                'agent' => 'SupportBot',
            ]);

            return response()->json([
                'message' => $response->text
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
