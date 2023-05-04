<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function conversations()
    {
        $user = Auth::user();
        $conversations = Conversation::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->with(['messages' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->limit(1)
            )
            ->get();

        return $conversations;
    }

    public function messages(Conversation $conversation)
    {
        return $conversation->messages()->with('sender')->get();

    }

    public function send_messages(Request $request, Conversation $conversation)
    {
        $validatedData = $request->validate([
            'body' => 'required|string',
        ]);

        $message = new Message;
        $message->body = $validatedData['body'];
        $message->conversation_id = $conversation->id;
        $message->sender_id = Auth::user()->id;
        $message->save();

        return response()->json(['message' => 'Message created successfully'], 201);
    }

    public function clearMessages(Request $request, Conversation $conversation)
    {
        Message::where('conversation_id', $conversation->id)->delete();

        return response()->json([
            'message' => 'Message cleared successfully',
        ], 201);
    }

}
