<?php

namespace App\Response\Manager\api;

use App\Events\MessageEvent;
use App\Http\Resources\GroupChatResource;
use App\Http\Resources\GroupMessageResource;
use App\Models\GroupChat;
use App\Models\GroupMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GroupMessagesBlob;

class GroupMessageManagerResponse
{
    public function getGroupMessagesWithUsers(Request $request, $groupId)
    {
        $groupChat = GroupChat::where('id', $groupId)->first();
        $request->roomOwnerHide = true;
        if (!$groupChat) {
            return response()->json([
                'error' => 'No GroupChat record found for the current user.',
            ], 404);
        }

        $page = $request->input('page', 0);
        $perPage = 10;
        $groupMessages = GroupMessage::where('group_id', $groupId)->paginate($perPage);

        if ($page == 0 || !$page) {
            $page = $groupMessages->lastPage();
            $groupMessages = GroupMessage::where('group_id', $groupId)->paginate($perPage, ['*'], 'page', $page);
        }

        foreach ($groupMessages as $message) {
            $message->user = User::find($message->user_id);
        }

        $data = [
            'current_page' => $groupMessages->currentPage(),
            'data' => GroupMessageResource::collection($groupMessages),
            'first_page_url' => $groupMessages->url(1),
            'from' => $groupMessages->firstItem(),
            'last_page' => $groupMessages->lastPage(),
            'last_page_url' => $groupMessages->url($groupMessages->lastPage()),
            'next_page_url' => $groupMessages->nextPageUrl(),
            'path' => $groupMessages->path(),
            'per_page' => $groupMessages->perPage(),
            'prev_page_url' => $groupMessages->previousPageUrl(),
            'to' => $groupMessages->lastItem(),
            'total' => $groupMessages->total(),
        ];

        return response()->json([
            'data' => [
                'group_chat' => new GroupChatResource($groupChat),
                'group_messages' => $data,
            ],
        ]);
    }

    public function index()
    {
        $pagination = 10;

        if (request()->input("items")) {
            $pagination = request()->input("items");
        }

        $groupMessages = GroupMessage::with(['groupChat.groupUsers', 'user'])
            ->orderBy('id', request()->input("order") ? request()->input("order") : 'desc')
            ->paginate($pagination, ['id', 'group_id', 'user_id', 'content', 'type', 'group_messages_blob_id', 'created_at']);

        $formattedMessages = $groupMessages->map(function ($message) {
            $formattedMessage = [
                'id' => $message->id,
                'group_id' => $message->group_id,
                'user_id' => $message->user_id,
                'content' => $message->content,
                'type' => $message->type,
                'group_messages_blob_id' => $message->group_messages_blob_id,
                'created_at' => $message->created_at,
            ];

            if ($message->user) {
                $formattedMessage['sender_name'] = $message->user->firstname . ' ' . $message->user->lastname;
            } else {
                $formattedMessage['sender_name'] = 'Unknown User';
            }

            if ($message->groupChat && $message->groupChat->name) {
                $formattedMessage['group_name'] = $message->groupChat->name;
            } else {
                $formattedMessage['group_name'] = 'Unknown Group Name';
            }

            return $formattedMessage;
        });

        return response()->json([
            'data' => [
                'current_page' => $groupMessages->currentPage(),
                'data' => $formattedMessages,
                'first_page_url' => $groupMessages->url(1),
                'from' => $groupMessages->firstItem(),
                'last_page' => $groupMessages->lastPage(),
                'last_page_url' => $groupMessages->url($groupMessages->lastPage()),
                'next_page_url' => $groupMessages->nextPageUrl(),
                'path' => $groupMessages->path(),
                'per_page' => $groupMessages->perPage(),
                'prev_page_url' => $groupMessages->previousPageUrl(),
                'to' => $groupMessages->lastItem(),
                'total' => $groupMessages->total(),
            ],
        ]);
    }

    public function show(GroupMessage $groupMessage)
    {
        return response()->json([
            'data' => $groupMessage,
        ]);
    }

    public function store(Request $request)
    {
        $groupMessage = GroupMessage::create($request->all());
        $user = auth()->user();

        if ($this->isConnectedToInternet()) {
            event(new MessageEvent(null, $request->content, $user->id, $request->group_id, $request->fullname, $request->group_messages_blob_id));
        }

        return response()->json([
            'message' => 'Group message created successfully.',
            'data' => $groupMessage,
        ]);
    }

    private function isConnectedToInternet()
    {
        $url = 'https://www.google.com';
        $headers = @get_headers($url);

        return $headers && strpos($headers[0], '200 OK') !== false;
    }

    public function update(Request $request, GroupMessage $groupMessage)
    {
        $groupMessage->update($request->all());

        return response()->json([
            'message' => 'Group message updated successfully.',
            'data' => $groupMessage,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->type != 'admin' || !request()->input('role') || request()->input('role') != 'admin') {
            return response()->json(['message' => "You don't have permission to remove the data."], 404);
        }

        $groupMessage = GroupMessage::findOrFail($id);
        if ($groupMessage) {
            $messageId = $groupMessage->group_messages_blob_id;

            $groupMessagesBlob = GroupMessagesBlob::where('group_messages_blob_id', $messageId);
            $groupMessagesBlob->delete();
            $groupMessage->delete();
        }

        return response()->json([
            'message' => 'Group messages deleted successfully.',
        ]);
    }

    public function deleteGroupMessages($groupId)
    {
        $groupChat = GroupChat::where('id', $groupId)->first();
        if (!$groupChat) {
            return response()->json([
                'error' => 'No GroupChat record found for the current user.',
            ], 404);
        }

        $groupMessages = GroupMessage::where('group_id', $groupId);
        $messageId = $groupMessages->pluck('group_messages_blob_id');
        $groupMessagesBlob = GroupMessagesBlob::whereIn('group_messages_blob_id', $messageId);
        $groupMessagesBlob->delete();

        GroupMessage::where('group_id', $groupId)->delete();
        return response()->json([
            'message' => 'All group messages have been deleted successfully.',
        ]);
    }
}
