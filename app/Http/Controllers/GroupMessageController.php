<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Http\Controllers\Controller;
use App\Models\GroupChat;
use App\Models\GroupMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupMessageController extends Controller
{
    public function getGroupMessagesWithUsers(Request $request, $groupId)
    {
        // Fetch the first GroupChat record associated with the current user
        $groupChat = GroupChat::where('id', $groupId)->first();

        // If no matching GroupChat record is found, return an error response
        if (!$groupChat) {
            return response()->json([
                'error' => 'No GroupChat record found for the current user.',
            ], 404);
        }

        $page = $request->input('page', 0); // Get the 'page' parameter from the request, defaulting to 1 if not provided
        $perPage = 20;

        $groupMessages = GroupMessage::where('group_id', $groupId)->paginate($perPage);

        if ($page == 0 || !$page) {
            $page = $groupMessages->lastPage();
            $groupMessages = GroupMessage::where('group_id', $groupId)->paginate($perPage, ['*'], 'page', $page);
        }

        // Loop through the GroupMessage records and include the associated User record for each message
        foreach ($groupMessages as $message) {
            $message->user = User::find($message->user_id);
        }

        return response()->json([
            'data' => [
                'group_chat' => $groupChat,
                'group_messages' => $groupMessages,
            ],
        ]);
    }

    // Get all group messages
    public function index()
    {
        $groupMessages = GroupMessage::with(['groupChat.groupUsers', 'user'])
            ->paginate(10, ['id', 'group_id', 'user_id', 'content', 'created_at']);

        $formattedMessages = $groupMessages->map(function ($message) {
            return [
                'id' => $message->id,
                'group_id' => $message->group_id,
                'user_id' => $message->user_id,
                'sender_name' => $message->user->firstname . ' ' . $message->user->lastname,
                'group_name' => $message->groupChat->name,
                'created_at' => $message->created_at
            ];
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

    // Get a specific group message by ID
    public function show(GroupMessage $groupMessage)
    {
        return response()->json([
            'data' => $groupMessage,
        ]);
    }

    // Create a new group message
    public function store(Request $request)
    {
        $groupMessage = GroupMessage::create($request->all());

        $user = auth()->user();

        // Check for internet connectivity before firing the event
        if ($this->isConnectedToInternet()) {
            event(new MessageEvent(null, $request->content, $user->id, $request->group_id, $request->fullname));
        }

        return response()->json([
            'message' => 'Group message created successfully.',
            'data' => $groupMessage,
        ]);
    }

    private function isConnectedToInternet()
    {
        $url = 'https://www.google.com'; // or any other reliable website
        $headers = @get_headers($url);

        return $headers && strpos($headers[0], '200 OK') !== false;
    }

    // Update a specific group message by ID
    public function update(Request $request, GroupMessage $groupMessage)
    {
        $groupMessage->update($request->all());

        return response()->json([
            'message' => 'Group message updated successfully.',
            'data' => $groupMessage,
        ]);
    }

    // Delete a specific group message by ID
    public function destroy(Request $request, $id)
    {

        if ($request->input('role') != 'admin') {
            return response()->json(['message' => "You don't have permission to remove the data."], 404);
        }

        GroupMessage::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Group messages deleted successfully.',
        ]);
    }

    public function deleteGroupMessages($groupId)
    {
        // Fetch the GroupChat record associated with the current user and the specified group ID
        $groupChat = GroupChat::where('id', $groupId)->first();

        // If no matching GroupChat record is found, return an error response
        if (!$groupChat) {
            return response()->json([
                'error' => 'No GroupChat record found for the current user.',
            ], 404);
        }

        // Delete all GroupMessage records associated with the group
        GroupMessage::where('group_id', $groupId)->delete();

        return response()->json([
            'message' => 'All group messages have been deleted successfully.',
        ]);
    }

}
