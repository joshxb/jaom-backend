<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GroupUser;
use Illuminate\Http\Request;

class GroupUserController extends Controller
{
    // Get all group users
    public function index()
    {
        $groupUsers = GroupUser::all();

        return response()->json([
            'data' => $groupUsers,
        ]);
    }

    // Create a new group user
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'group_id' => 'required|integer',
            'user_ids' => 'required|array',
            'user_ids.*' => 'required|integer',
        ]);

        $userIds = $validatedData['user_ids'];

        $groupUsers = [];
        for ($i = 0; $i < count($userIds); $i++) {
            $groupUser = GroupUser::create([
                'group_id' => $validatedData["group_id"],
                'user_id' => $userIds[$i],
            ]);
            $groupUsers[] = $groupUser;
        }

        return response()->json([
            'message' => 'Group users created successfully.',
            'data' => $groupUsers,
        ]);
    }

    // Get a specific group user by ID
    public function show(GroupUser $groupUser)
    {
        return response()->json([
            'data' => $groupUser,
        ]);
    }

    // Update a specific group user by ID
    public function update(Request $request, GroupUser $groupUser)
    {
        $groupUser->update($request->all());

        return response()->json([
            'message' => 'Group user updated successfully.',
            'data' => $groupUser,
        ]);
    }
    // Delete a specific group user by ID
    public function destroy(GroupUser $groupUser)
    {
        $groupUser->delete();

        return response()->json([
            'message' => 'Group user deleted successfully.',
        ]);
    }
}
