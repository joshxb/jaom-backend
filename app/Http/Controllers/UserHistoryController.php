<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserHistoryController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        // If the user is authenticated, assign the current user's ID as user_id
        $userId = $user ? $user->id : null;

        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $data['user_id'] = $userId;

        $userHistory = UserHistory::create($data);

        return response()->json($userHistory, 201);
    }

    public function index()
    {
        $user = Auth::user();

        // If the user is authenticated, retrieve all user_history records belonging to the current user
        $userHistories = $user ? UserHistory::where('user_id', $user->id)->get() : null;

        if (!$userHistories) {
            return response()->json(['error' => 'User history not found.'], 404);
        }

        return response()->json($userHistories);
    }

    public function show($id)
    {
        $user = Auth::user();

        // If the user is authenticated, retrieve the user_history by ID only if it belongs to the current user
        $userHistory = $user ? UserHistory::where('id', $id)->where('user_id', $user->id)->first() : null;

        if (!$userHistory) {
            return response()->json(['error' => 'User history not found.'], 404);
        }

        return response()->json($userHistory);
    }

    public function destroy($id)
    {
        $user = Auth::user();

        // If the user is authenticated, retrieve the user_history by ID only if it belongs to the current user
        $userHistory = $user ? UserHistory::where('id', $id)->where('user_id', $user->id)->first() : null;

        if (!$userHistory) {
            return response()->json(['error' => 'User history not found.'], 404);
        }

        $userHistory->delete();

        return response()->json(['message' => 'User history deleted.']);
    }

    public function destroyAll()
    {
        $user = Auth::user();

        // If the user is authenticated, retrieve all user_history records belonging to the current user
        $userHistories = $user ? UserHistory::where('user_id', $user->id)->get() : null;

        if (!$userHistories) {
            return response()->json(['error' => 'User history not found.'], 404);
        }

        $userHistories->each->delete();

        return response()->json(['message' => 'User history logs successfully deleted!']);
    }

}
