<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function conversations()
    {
        $user = Auth::user();
        $conversations = Conversation::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->with([
                'messages' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->limit(1)
            )
            ->paginate(10); // Pagination with 10 items per page

        $conversationIds = $conversations->pluck('id')->toArray();
        $messageCounts = Message::whereIn('conversation_id', $conversationIds)
            ->selectRaw('conversation_id, COUNT(*) as count')
            ->groupBy('conversation_id')
            ->pluck('count', 'conversation_id')
            ->toArray();

        $conversations->getCollection()->each(function ($conversation) use ($user, $messageCounts) {
            $otherUserId = ($user->id === $conversation->user1_id) ? $conversation->user2_id : $conversation->user1_id;
            $conversation->other_user_id = $otherUserId;
            $conversation->messages_count = $messageCounts[$conversation->id] ?? 0;
        });

        return $conversations;
    }

    public function first_conversations()
    {
        $user = Auth::user();
        $conversations = Conversation::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->with([
                'messages' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->limit(1)
            )
            ->first();

        return $conversations;
    }

    public function messages(Conversation $conversation)
    {
        $perPage = 10;
        $totalMessages = $conversation->messages()->count();
        $maxPage = ceil($totalMessages / $perPage);
        $currentPage = request()->input('page') === '0' ? $maxPage : request()->input('page', $maxPage);

        $messages = $conversation->messages()
            ->with('sender')
            ->forPage($currentPage, $perPage)
            ->get();

        return new LengthAwarePaginator(
            $messages,
            $totalMessages,
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );
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
