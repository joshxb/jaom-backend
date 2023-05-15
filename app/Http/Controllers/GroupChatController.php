<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GroupChat;
use App\Models\GroupMessage;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupChatController extends Controller
{
    public function indexWithCurrentUser()
    {
        $user = Auth::user();

        $results = DB::select("
        SELECT DISTINCT c.name, c.id
FROM group_chats c
LEFT JOIN group_user u ON u.group_id = c.id OR u.user_id = c.user_id
LEFT JOIN group_messages m ON m.group_id = c.id
WHERE c.user_id = ? OR (u.user_id = ? AND c.user_id != ?)
ORDER BY m.created_at DESC;
    ", [$user->id, $user->id, $user->id]);

        return response()->json([
            'data' => $results,
        ]);
    }

    public function getFirstGroupMessages()
    {
        $user = Auth::user();

        // Fetch the first GroupChat record associated with the current user
        $groupChat = GroupChat::where('user_id', $user->id)->first();

        // If no matching GroupChat record is found, return an error response
        if (!$groupChat) {
            return response()->json([
                'error' => 'No GroupChat record found for the current user.',
            ], 404);
        }

        // Fetch the corresponding GroupMessage records for the first GroupChat record
        $groupMessages = GroupMessage::where('group_id', $groupChat->id)->get();

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

    public function index()
    {
        $groupChats = GroupChat::all();
        return response()->json($groupChats);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required',
            'user_ids' => 'array', // Ensure user_ids is an array
            'user_ids.*' => 'exists:users,id', // Validate that each user_id exists in the users table
        ]);

        $groupChat = GroupChat::where("user_id", $user->id)
            ->where("name", $validatedData["name"])
            ->first();

        if (!$groupChat) {
            $groupChat = GroupChat::create([
                "name" => $validatedData["name"],
                "user_id" => $user->id,
            ]);
        }

        $userIds = $validatedData['user_ids'];

        $groupId = $groupChat->id;

        $groupUsers = [];
        foreach ($userIds as $userId) {
            $groupUser = GroupUser::create([
                'group_id' => $groupId,
                'user_id' => $userId,
            ]);
            $groupUsers[] = $groupUser;
        }

        return response()->json([
            'message' => 'Group chat created successfully.',
            'data' => $groupChat,
            'data2' => $groupUsers,
        ]);
    }

    public function show(Request $request)
    {
        $result = GroupChat::where("id", $request->groupId)->first();
        return response()->json([
            "data" => $result,
        ]);
    }

    public function update(Request $request, GroupChat $groupChat)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        $validatedData['user_id'] = Auth::id(); // Set the user_id to the current user's ID

        $groupChat->update($validatedData);

        return response()->json([
            'message' => 'Group chat updated successfully.',
            'data' => $groupChat,
        ]);
    }
    public function destroy(GroupChat $groupChat)
    {
        $groupChat->delete();

        return response()->json([
            'message' => 'Group chat deleted successfully.',
        ]);
    }

}
