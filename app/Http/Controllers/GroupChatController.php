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
    public function groupChatCounts() {
        $groupChatCount = GroupChat::count();
        return response()->json(['room_count' => $groupChatCount]);
    }

    public function indexWithCurrentUser()
    {
        $user = Auth::user();
        $perPage = 10;

        // Subquery to get the latest message IDs for each group
        $latestMessageIds = DB::table('group_messages')
            ->select('group_id', DB::raw('MAX(id) as latest_message_id'))
            ->groupBy('group_id');

        $results = GroupChat::selectRaw('c.name, c.user_id, c.id, c.left_active_count')
            ->from('group_chats as c')
            ->leftJoin('group_user as u', 'u.group_id', '=', 'c.id')
            ->leftJoinSub($latestMessageIds, 'm', function ($join) {
                $join->on('c.id', '=', 'm.group_id');
            })
            ->where('c.user_id', $user->id)
            ->orWhere(function ($query) use ($user) {
                $query->where('u.user_id', $user->id)
                    ->where('c.user_id', '!=', $user->id);
            })
            ->groupBy('c.name', 'c.user_id', 'c.id', 'c.left_active_count')
            ->orderByDesc('m.latest_message_id') // Use the alias from the subquery
            ->paginate($perPage);

        $result2 = GroupChat::selectRaw('c.id')
            ->from('group_chats as c')
            ->leftJoin('group_user as u', 'u.group_id', '=', 'c.id')
            ->leftJoinSub($latestMessageIds, 'm', function ($join) {
                $join->on('c.id', '=', 'm.group_id');
            })
            ->where('c.user_id', $user->id)
            ->orWhere(function ($query) use ($user) {
                $query->where('u.user_id', $user->id)
                    ->where('c.user_id', '!=', $user->id);
            })
            ->groupBy('c.id')
            ->orderByDesc('m.latest_message_id') // Use the alias from the subquery
            ->get();

        $results->each(function ($item) {
            $item->total_messages = $this->getTotalMessages($item->id);
            if ($item->user_id !== Auth::user()->id) {
                $item->left_active_count = $this->getLeftActiveCount($item->id);
            }
        });

        return response()->json([
            'data' => $results->items(),
            'meta' => [
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
                'last_page' => ceil($result2->count() / $perPage),
            ],
        ]);
    }
    private function getTotalMessages($groupId)
    {
        return GroupMessage::where('group_id', $groupId)->count();
    }

    private function getLeftActiveCount($groupId)
    {
        return GroupUser::select('left_active_count')
            ->where('group_id', $groupId)
            ->first()->left_active_count;
    }

    public function getFirstGroupMessages(Request $request)
    {
        $user = Auth::user();

        $results = DB::select("
        SELECT c.name, c.id
        FROM group_chats c
        LEFT JOIN group_user u ON u.group_id = c.id OR u.user_id = c.user_id
        LEFT JOIN group_messages m ON m.group_id = c.id
        WHERE c.user_id = ? OR (u.user_id = ? AND c.user_id != ?)
        ORDER BY m.id DESC
        LIMIT 1;
    ", [$user->id, $user->id, $user->id]);

        $data = [];

        if (!empty($results)) {
            $data = [
                'id' => $results[0]->id,
                'name' => $results[0]->name,
            ];
        }

        $groupChat = null;
        if (isset($data["id"])) {
            $groupChat = GroupChat::where("id", $data["id"])->first();
        }
        // Fetch the corresponding GroupMessage records for the first GroupChat record
        $page = $request->input('page', 0); // Get the 'page' parameter from the request, defaulting to 1 if not provided
        $perPage = 20;

        $groupMessages = null;
        if (isset($data["id"])) {
            $groupMessages = GroupMessage::where('group_id', $data['id'])->paginate($perPage);
        }
        if ($page == 0 || !$page) {
            if (isset($data["id"])) {
                $page = $groupMessages->lastPage();
                $groupMessages = GroupMessage::where('group_id', $data['id'])->paginate($perPage, ['*'], 'page', $page);
            }
        }

        if (isset($data["id"])) {
            // Loop through the GroupMessage records and include the associated User record for each message
            foreach ($groupMessages as $message) {
                $message->user = User::find($message->user_id);
            }
        }
        return response()->json([
            'data' => [
                'group_chat' => $groupChat,
                'group_messages' => $groupMessages,
            ],
        ]);
    }

    public function getSpecificGroupMessages(Request $request)
    {
        $user = Auth::user();

        $results = DB::select("
        SELECT c.name, c.id
        FROM group_chats c
        LEFT JOIN group_user u ON u.group_id = c.id OR u.user_id = c.user_id
        LEFT JOIN group_messages m ON m.group_id = c.id
        WHERE (c.user_id = ? OR (u.user_id = ? AND c.user_id != ?)) AND c.id = ?
        GROUP BY c.name, c.id
        ORDER BY m.created_at DESC
        LIMIT 1;
    ", [$user->id, $user->id, $user->id, $request->group_id]);

        $data = [];

        if (!empty($results)) {
            $data = [
                'id' => $results[0]->id,
                'name' => $results[0]->name,
            ];
        }

        $groupChat = null;

        if (isset($data["id"])) {
            $groupChat = GroupChat::where("id", $data["id"])->first();
        }
        // Fetch the corresponding GroupMessage records for the first GroupChat record
        $page = $request->input('page', 0); // Get the 'page' parameter from the request, defaulting to 1 if not provided
        $perPage = 20;

        $groupMessages = null;
        if (isset($data["id"])) {
            $groupMessages = GroupMessage::where('group_id', $data['id'])->paginate($perPage);
        }

        if ($page == 0 || !$page) {
            if (isset($data["id"])) {
                $page = $groupMessages->lastPage();
                $groupMessages = GroupMessage::where('group_id', $data['id'])->paginate($perPage, ['*'], 'page', $page);
            }
        }

        if (isset($data["id"])) {
            // Loop through the GroupMessage records and include the associated User record for each message
            foreach ($groupMessages as $message) {
                $message->user = User::find($message->user_id);
            }
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
        $groupChats = GroupChat::with("user")->paginate(10);
        return response()->json($groupChats);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required',
            'user_ids' => 'array',
            // Ensure user_ids is an array
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

        $groupUser = GroupUser::where("group_id", $group_id);
        $groupMessage = GroupMessage::where("group_id", $group_id);

        if ($groupUser) {
            $groupUser->delete();
        }

        if ($groupMessage) {
            $groupMessage->delete();
        }

        $groupChat->delete();

        return response()->json([
            'message' => 'Group chat deleted successfully!',
        ]);
    }

    public function destroyV2($group_id)
    {
        $user = Auth::user();

        if ($user->type != 'admin' || !request()->input('role') || request()->input('role') != 'admin') {
            return response()->json(['message' => "You don't have permission to remove the room."], 404);
        }

        $groupChat = GroupChat::find($group_id);

        if (!$groupChat) {
            return response()->json([
                'message' => "Group chat not found!",
            ], 404); // Return a 404 Not Found status code if the group chat doesn't exist.
        }

        $groupUser = GroupUser::where("group_id", $group_id);
        $groupMessage = GroupMessage::where("group_id", $group_id);

        if ($groupUser) {
            $groupUser->delete();
        }

        if ($groupMessage) {
            $groupMessage->delete();
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

    public function updateActiveLeftGroupConvo(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'messages_count' => 'required|integer',
            'id' => 'required|integer',
        ]);

        $groupChat = GroupChat::where('id', $validatedData['id'])->where('user_id', $user->id)->first();

        if ($groupChat) {
            $groupChat->update([
                'left_active_count' => $validatedData['messages_count'],
            ]);

            return response()->json(['message' => 'updated successfully']);
        } else {
            $groupUser = GroupUser::where('group_id', $validatedData['id'])->where('user_id', $user->id)->first();

            if ($groupUser) {
                $groupUser->update([
                    'left_active_count' => $validatedData['messages_count'],
                ]);

                return response()->json(['message' => 'updated successfully']);
            } else {
                return response()->json(['error' => 'Group Conversation not found'], 404);
            }
        }
    }
}
