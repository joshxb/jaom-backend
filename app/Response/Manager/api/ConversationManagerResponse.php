<?php

namespace App\Response\Manager\api;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationManagerResponse
{
    public function conversationCounts()
    {
        $conversationCount = Conversation::count();
        return response()->json(['chat_count' => $conversationCount]);
    }

    public function add_conversation(Request $request)
    {
        $user1_id = $request->user1_id;
        $user2_id = $request->user2_id;

        $existingConversation = Conversation::where(function ($query) use ($user1_id, $user2_id) {
            $query->where('user1_id', $user1_id)->where('user2_id', $user2_id);
        })->orWhere(function ($query) use ($user1_id, $user2_id) {
            $query->where('user1_id', $user2_id)->where('user2_id', $user1_id);
        })->first();

        if ($existingConversation) {
            return response()->json([
                'message' => 'Conversation already exists',
                'conversation' => $existingConversation,
            ], 200);
        }

        $conversation = new Conversation;
        $conversation->user1_id = $user1_id;
        $conversation->user2_id = $user2_id;
        $conversation->save();

        return response()->json([
            'message' => 'Conversation created successfully',
            'conversation' => $conversation,
        ], 201);
    }

    public function getOtherUserId($conversation)
    {
        $currentUserId = auth()->id();
        if ($conversation->user1_id === $currentUserId) {
            return response()->json(['other_user_id' => $conversation->user2_id]);
        } else if ($conversation->user2_id === $currentUserId) {
            return response()->json(['other_user_id' => $conversation->user1_id]);
        } else {
            return response()->json(['message' => 'User is not part of this conversation.'], 403);
        }
    }

    public function getFirstConversationId()
    {
        $user = Auth::user();
        $conversation = Conversation::leftJoin('messages', 'conversations.id', '=', 'messages.conversation_id')
            ->where('conversations.user1_id', $user->id)
            ->orWhere('conversations.user2_id', $user->id)
            ->orderByRaw('messages.created_at DESC')
            ->select('conversations.id')
            ->first();

        if (!$conversation) {
            return null;
        }

        return $conversation->id;
    }

    public function deleteConversation(Request $request, Conversation $conversation)
    {
        Message::where('conversation_id', $conversation->id)->delete();
        Conversation::where('id', $conversation->id)->delete();

        return response()->json([
            'message' => 'Conversation deleted successfully',
        ], 201);
    }

    public function updateActiveLeftConvo(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'messages_count' => 'required|integer',
            'id' => 'required|integer',
        ]);

        $conversation = Conversation::where('id', $validatedData['id'])->where('user1_id', $user->id)->first();

        if ($conversation) {
            $conversation->update([
                'user1_left_count' => $validatedData['messages_count'],
            ]);

            return response()->json(['message' => 'updated successfully']);
        } else {
            $conversation = Conversation::where('id', $validatedData['id'])->where('user2_id', $user->id)->first();

            if ($conversation) {
                $conversation->update([
                    'user2_left_count' => $validatedData['messages_count'],
                ]);

                return response()->json(['message' => 'updated successfully']);
            } else {
                return response()->json(['error' => 'Conversation not found'], 404);
            }
        }
    }
}
