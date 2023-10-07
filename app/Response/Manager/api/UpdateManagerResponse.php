<?php

namespace App\Response\Manager\api;

use App\Models\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateManagerResponse
{
    public function updatesCounts()
    {
        $updateCount = Update::count();
        return response()->json(['update_count' => $updateCount]);
    }

    public function allUpdates()
    {
        $user = Auth::user();
        if ($user->type !== 'admin') {
            return response()->json(['message' => "You don't have permission to get the data."], 403);
        }

        $updates = Update::orderByDesc('created_at')->paginate(10);
        return response()->json([$updates]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $per_page = $request->input('per_page', 10);

        $page = $request->input('page');
        $page = 10 * ($page - 1);

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
            ->skip($page)
            ->take($per_page)
            ->get()
            ->map(function ($update) {
                return [
                    'id' => $update->id,
                    'user_id' => $update->user_id,
                    'firstname' => $update->user->firstname ? $update->user->firstname : null,
                    'lastname' => $update->user->lastname ?  $update->user->lastname : null,
                    'nickname' => $update->user->nickname ? $update->user->nickname : null,
                    'subject' => $update->subject,
                    'content' => $update->content,
                    'permission' => $update->permission,
                    'formatted_created_at' => $update->created_at->format('F j, Y \a\t g:i a - l'),
                    'max_page' => ceil(($update->count()) / 10)
                ];
            });

        return response()->json(['result' => $results]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
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

    public function updatePermission($id)
    {
        $user = Auth::user();

        if ($user->type != 'admin' || !request()->input('role') || request()->input('role') != 'admin') {
            return response()->json(['message' => "You don't have permission to remove the room."], 404);
        }

        $update = Update::find($id);
        if (!$update) {
            return response()->json(['message' => 'Update not found'], 404);
        }

        $update->permission = request()->permission;
        $update->save();

        return response()->json([
            'data' => $update,
            'message' => "Permission updated successfully!"
    ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $update = Update::find($id);
        if (!$update) {
            return response()->json(['message' => 'Update not found'], 404);
        }

        if ($user->type == 'admin' && request()->has('role') && request()->input('role') == 'admin') {
        } else if ($update->user_id !== $user->id) {
            return response()->json(['message' => "You are not authorize to delete this updates"], 403);
        }

        $update->delete();
        return response()->json(['message' => 'Update(s) successfully deleted']);
    }
}
