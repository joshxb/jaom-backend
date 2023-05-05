<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GroupChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupChatController extends Controller
{
    public function indexWithCurrentUser()
    {
        $user = Auth::user();

        // Fetch all GroupChat records associated with the current user
        $groupChats = GroupChat::where('user_id', $user->id)->get();

        return response()->json([
            'data' => $groupChats,
        ]);
    }

    public function index()
    {
        $groupChats = GroupChat::all();
        return response()->json($groupChats);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'user_id' => 'required|integer',
        ]);

        $groupChat = GroupChat::create($validatedData);

        return response()->json([
            'message' => 'Group chat created successfully.',
            'data' => $groupChat,
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
