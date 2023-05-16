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

        $results = DB::select("
        SELECT DISTINCT c.name, c.id
FROM group_chats c
LEFT JOIN group_user u ON u.group_id = c.id OR u.user_id = c.user_id
LEFT JOIN group_messages m ON m.group_id = c.id
WHERE c.user_id = ? OR (u.user_id = ? AND c.user_id != ?)
ORDER BY m.created_at DESC LIMIT 1;
    ", [$user->id, $user->id, $user->id]);

        $data = [];

        if (!empty($results)) {
            $data = [
                'id' => $results[0]->id,
                'name' => $results[0]->name,
            ];
        }

        $groupChat = GroupChat::where("id", $data["id"])->first();
        // Fetch the corresponding GroupMessage records for the first GroupChat record
        $groupMessages = GroupMessage::where('group_id', $data["id"])->get();

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

    public function destroy($user_id, $group_id)
    {
        $user = Auth::user();

        if ($user_id != $user->id) {
            return response()->json([
                'message' => "You are not authorized to delete the GroupChat!",
            ], 403); // Return a 403 Forbidden status code for unauthorized access.
        }

        $groupChat = GroupChat::find($group_id);

        if (!$groupChat) {
            return response()->json([
                'message' => "Group chat not found!",
            ], 404); // Return a 404 Not Found status code if the group chat doesn't exist.
        }

        $groupChat->delete();

        return response()->json([
            'message' => 'Group chat deleted successfully!',
        ]);
    }

    public function destroySelectedGroupUsers(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'user_ids' => 'required|array', // Ensure user_ids is an array and required
        ]);

        $userIds = $validatedData['user_ids'];

        GroupUser::where('group_id', $request->group_id)
            ->whereIn('user_id', $userIds)
            ->delete();

        return response()->json([
            'message' => 'Group users deleted successfully.',
        ]);
    }

}
