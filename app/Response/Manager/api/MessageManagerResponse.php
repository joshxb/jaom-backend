<?php

namespace App\Response\Manager\api;

use App\Events\MessageEvent;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class MessageManagerResponse
{
    public function all_conversations(Request $request)
    {
        $user = Auth::user();
        if ($user->type != 'admin' || !request()->input('role') || request()->input('role') != 'admin') {
            return response()->json(['message' => "You don't have permission to access the data."], 404);
        }

        $conversations = Conversation::with([
            'messages' => function ($query) {
                $query->select('id', 'conversation_id', 'sender_id', 'body', 'created_at')
                    ->orderBy('created_at', 'asc');
            }
        ])->get();

        $allMessages = [];
        foreach ($conversations as $conversation) {
            foreach ($conversation->messages as $message) {
                $senderId = $message->sender_id;
                $receiverId = ($senderId === $conversation->user1_id) ? $conversation->user2_id : $conversation->user1_id;

                $formattedMessage = [
                    'id' => $message->id,
                    'sender_id' => $senderId,
                    'receiver_id' => $receiverId,
                    'body' => $message->body,
                    'date' => $message->created_at->format('F j, Y'),
                    'time' => $message->created_at->format('g:i A'),
                ];

                $allMessages[] = $formattedMessage;
            }
        }

        $totalMessages = count($allMessages);

        $perPage = $request->input('per_page', 10);

        $currentPage = $request->input('page', 1);
        $lastPage = ceil($totalMessages / $perPage);

        $messagesOnCurrentPage = array_slice($allMessages, ($currentPage - 1) * $perPage, $perPage);

        return [
            'current_page' => $currentPage,
            'messages' => $messagesOnCurrentPage,
            'last_page' => $lastPage,
            'total' => $totalMessages,
            'per_page' => $perPage,
        ];
    }

    public function conversations()
    {
        $user = Auth::user();
        $conversationsTest = Conversation::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->with([
                'messages' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ])
            ->latest()
            ->first();

        $conversationsTest2 = Conversation::where('user1_id', $user->id)
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
            )->latest()
            ->first();

        $result = null;
        try {
            if ($conversationsTest->created_at > $conversationsTest2->messages[0]->created_at) {
                $result = Conversation::where('user1_id', $user->id)
                    ->orWhere('user2_id', $user->id)
                    ->with([
                        'messages' => function ($query) {
                            $query->orderBy('created_at', 'desc');
                        }
                    ])
                    ->orderByDesc('created_at')
                    ->paginate(10);
            } else {
                $result = Conversation::where('user1_id', $user->id)
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
                    ->paginate(10);
            }
        } catch (\Throwable $th) {
            $result = Conversation::where('user1_id', $user->id)
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
                ->paginate(10);
        }

        $conversationIds = $result->pluck('id')->toArray();
        $messageCounts = Message::whereIn('conversation_id', $conversationIds)
            ->selectRaw('conversation_id, COUNT(*) as count')
            ->groupBy('conversation_id')
            ->pluck('count', 'conversation_id')
            ->toArray();

        $result->getCollection()->each(function ($conversation) use ($user, $messageCounts) {
            $otherUserId = ($user->id === $conversation->user1_id) ? $conversation->user2_id : $conversation->user1_id;
            $conversation->other_user_id = $otherUserId;
            $conversation->messages_count = $messageCounts[$conversation->id] ?? 0;
        });

        return $result;
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
            ->latest()
            ->first();

        $conversations2 = Conversation::where('user1_id', $user->id)
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
            )->latest()
            ->first();

        if (!is_null($conversations) && !is_null($conversations2)) {
            if ($conversations->created_at > $conversations2->messages[0]->created_at) {
                return $conversations;
            } else {
                return $conversations2;
            }
        } else {
            return [];
        }
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

        request()->chatUser = true;
        return new LengthAwarePaginator(
            MessageResource::collection($messages),
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

        if ($this->isConnectedToInternet()) {
            event(new MessageEvent($conversation->id, $validatedData['body'], $request->other_user_id, null, null));
        }
        return response()->json(['message' => 'Message created successfully'], 201);
    }

    private function isConnectedToInternet()
    {
        $url = 'https://www.google.com';
        $headers = @get_headers($url);

        return $headers && strpos($headers[0], '200 OK') !== false;
    }

    public function clearMessages(Request $request, Conversation $conversation)
    {
        Message::where('conversation_id', $conversation->id)->delete();

        return response()->json([
            'message' => 'Message cleared successfully',
        ], 201);
    }

    public function deleteSpecificMessage(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->type != 'admin' || !request()->input('role') || request()->input('role') != 'admin') {
            return response()->json(['message' => "You don't have permission to remove the data."], 404);
        }

        Message::where('id', $id)->delete();

        return response()->json([
            'message' => 'Message removed successfully',
        ], 201);
    }
}
