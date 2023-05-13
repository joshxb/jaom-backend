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
        JOIN group_messages m ON m.group_id = c.id OR m.user_id = C.user_id
        WHERE c.user_id = ?
        OR (m.user_id = ? AND c.user_id != ?)
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
        // {
        //     "name": "J-Hope Group",
        //     "user_ids" : [
        //         1,14,139
        //     ]
        // }
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required',
            'user_ids' => '',
            'user_ids.*' => '',
        ]);

        $userIds = $validatedData['user_ids'];

        $groupChat = GroupChat::create([
            "name" => $validatedData["name"],
            "user_id" => $user->id
        ]);

        $groupId = GroupChat::where("name", $validatedData["name"])
            ->where("user_id", $user->id)
            ->first()->id;

        $groupUsers = [];
        if (count($userIds) > 0) {
            for ($i = 0; $i < count($userIds); $i++) {
                $groupUser = GroupUser::create([
                    'group_id' => $groupId,
                    'user_id' => $userIds[$i],
                ]);
                $groupUsers[] = $groupUser;
            }
        }

        return response()->json([
            'message' => 'Group chat created successfully.',
            'data' => $groupChat,
            'data2' => $groupUsers,
        ]);
    }

    public function show(GroupChat $groupChat)
    {
        return response()->json($groupChat);
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
