<?php

namespace App\Response\Manager\api;

use App\Models\UserHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserHistoryManagerResponse
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

    public function indexAll()
    {
        $user = Auth::user();
        $pagination = 10;

        if (request()->input("items")) {
            $pagination = request()->input("items");
        }

        if ($user->type == 'admin') {
            $userHistories = $user ? UserHistory::orderBy('id', request()->input("order") ? request()->input("order") : 'desc')
            ->paginate($pagination) : null;

            if (!$userHistories) {
                return response()->json(['error' => 'User history not found.'], 404);
            }
            return response()->json($userHistories);
        } else {
            return response()->json(['message' => 'Permission denied'], 401);
        }
    }

    public function index()
    {
        $user = Auth::user();

        $userHistories = $user ? UserHistory::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(20) : null;
        if (!$userHistories) {
            return response()->json(['error' => 'User history not found.'], 404);
        }

        return response()->json($userHistories);
    }

    public function show($id)
    {
        $user = Auth::user();

        $userHistory = $user ? UserHistory::where('id', $id)->where('user_id', $user->id)->first() : null;
        if (!$userHistory) {
            return response()->json(['error' => 'User history not found.'], 404);
        }

        return response()->json($userHistory);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $userHistory = null;

        if ($user->type == 'admin') {
            $userHistory = $user ? UserHistory::where('id', $id)->first() : null;
        } else {
            $userHistory = $user ? UserHistory::where('id', $id)->where('user_id', $user->id)->first() : null;
        }

        if (!$userHistory) {
            return response()->json(['error' => 'User history not found.'], 404);
        }

        $userHistory->delete();
        return response()->json(['message' => 'User history deleted.']);
    }

    public function destroyAll()
    {
        $user = Auth::user();
        $userHistories = $user ? UserHistory::where('user_id', $user->id)->get() : null;

        if (!$userHistories) {
            return response()->json(['error' => 'User history not found.'], 404);
        }

        $userHistories->each->delete();
        return response()->json(['message' => 'User history logs successfully deleted!']);
    }
}

