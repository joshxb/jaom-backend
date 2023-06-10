<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Update;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        $results = $user->updates()
            ->with('user')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where(function ($query) {
                        $query->where('permission', 'approved')
                            ->orWhere('permission', 'disapproved');
                    });
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id)
                    ->where('permission', 'approved');
            })
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($update) {
                return [
                    'id' => $update->id,
                    'user_id' => $update->user_id,
                    'firstname' => $update->user->firstname,
                    'lastname' => $update->user->lastname,
                    'subject' => $update->subject,
                    'content' => $update->content,
                    'permission' => $update->permission,
                    'formatted_created_at' => $update->created_at->format('F j, Y \a\t g:i a - l'),
                ];
            });

        return response()->json(['result' => $results]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        // if ($user->type !== 'admin') {
        //     return response()->json(['message' => "You are not authorize yet to add new updates. Contact to your administrator."], 403);
        // }
        $update = new Update;
        $update->subject = $request->subject;
        $update->content = $request->content;
        $update->user_id = $user->id;
        $update->permission = "disapproved";
        $update->save();

        return response()->json(['data' => $update], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $update = Update::find($id);

        if (!$update) {
            return response()->json(['message' => 'Update not found'], 404);
        }

        if ($update->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized access in this action.']);
        }

        return response()->json(['data' => $update]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        // if ($user->type !== 'admin') {
        //     return response()->json(['message' => "You are not authorize yet to add new updates. Contact to your administrator."], 403);
        // }
        $update = Update::find($id);
        if (!$update) {
            return response()->json(['message' => 'Update not found'], 404);
        }
        if ($update->user_id !== $user->id) {
            return response()->json(['message' => 'You are not authorize to modify this updates.'], 403);
        }

        $update->subject = $request->subject;
        $update->content = $request->content;
        $update->save();

        return response()->json(['data' => $update]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $update = Update::find($id);
        if (!$update) {
            return response()->json(['message' => 'Update not found'], 404);
        }
        if ($update->user_id !== $user->id) {
            return response()->json(['message' => "You are not authorize to delete this updates"], 403);
        }

        $update->delete();

        return response()->json(['message' => 'Record deleted']);
    }
}
