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

        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $userHistory = new UserHistory($data);

        if ($user) {
            $userHistory->user_id = $user->id;
        }

        $userHistory->save();

        return response()->json($userHistory, 201);
    }

    public function index()
    {
        $user = Auth::user();

        // If the user is authenticated, retrieve all user_history records belonging to the current user in descending order by 'created_at'
        $userHistories = $user ? UserHistory::where('user_id', $user->id)->orderBy('created_at', 'desc')->get() : null;

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
