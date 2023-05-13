<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GroupMessage;
use App\Models\GroupChat;
use App\Models\User;
use Illuminate\Http\Request;

class GroupMessageController extends Controller
{
    public function getGroupMessagesWithUsers($groupId)
    {
        // Fetch the first GroupChat record associated with the current user
        $groupChat = GroupChat::where('id', $groupId)->first();

        // If no matching GroupChat record is found, return an error response
        if (!$groupChat) {
            return response()->json([
                'error' => 'No GroupChat record found for the current user.',
            ], 404);
        }

        // Fetch the corresponding GroupMessage records for the first GroupChat record
        $groupMessages = GroupMessage::where('group_id', $groupId)->get();

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
        $groupMessages = GroupMessage::all();

        return response()->json([
            'data' => $groupMessages,
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

        return response()->json([
            'message' => 'Group message created successfully.',
            'data' => $groupMessage,
        ]);
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
    public function destroy(GroupMessage $groupMessage)
    {
        $groupMessage->delete();

        return response()->json([
            'message' => 'Group message deleted successfully.',
        ]);
    }

}
