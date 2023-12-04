<?php

namespace App\Response\Manager\api;

use App\Mail\UpdateNotificationEmail;
use App\Models\Notification;
use App\Models\Update;
use App\Models\UpdatesBlobs;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UpdateManagerResponse
{
    public function updatesCounts()
    {
        $updateCount = Update::count();
        return response()->json(['update_count' => $updateCount]);
    }

    public function allUpdates()
    {
        $pagination = 10;

        if (request()->input("items")) {
            $pagination = request()->input("items");
        }

        $user = Auth::user();
        if ($user->type !== 'admin') {
            return response()->json(['message' => "You don't have permission to get the data."], 403);
        }

        $updates = Update::orderBy('id', request()->input("order") ? request()->input("order") : 'desc')
            ->paginate($pagination);
        return response()->json([$updates]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $per_page = $request->input('per_page', 10);
        $filter = $request->input('filter', 'all');
        $order = $request->input('order', 'newest');
        $page = $request->input('page');
        $page = 10 * ($page - 1);

        $results = null;
        if ($filter === 'all') {
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
                ->orderBy('created_at', $order === 'newest' ? 'desc' : 'asc')
                ->skip($page)
                ->take($per_page)
                ->get()
                ->map(function ($update) {
                    return [
                        'id' => $update->id,
                        'user_id' => $update->user_id,
                        'firstname' => $update->user->firstname ? $update->user->firstname : null,
                        'lastname' => $update->user->lastname ? $update->user->lastname : null,
                        'nickname' => $update->user->nickname ? $update->user->nickname : null,
                        'subject' => $update->subject,
                        'content' => $update->content,
                        'permission' => $update->permission,
                        'type' => $update->type,
                        'updates_blob_id' => $update->updates_blob_id,
                        'formatted_created_at' => $update->created_at->format('F j, Y \a\t g:i a - l'),
                        'max_page' => ceil(($update->count()) / 10)
                    ];
                });
        } else if ($filter === 'current') {
            $results = $user->updates()
                ->with('user')
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->where(function ($query) {
                            $query->where('permission', 'approved')
                                ->orWhere('permission', 'disapproved');
                        });
                })
                ->orderBy('created_at', $order === 'newest' ? 'desc' : 'asc')
                ->skip($page)
                ->take($per_page)
                ->get()
                ->map(function ($update) {
                    return [
                        'id' => $update->id,
                        'user_id' => $update->user_id,
                        'firstname' => $update->user->firstname ? $update->user->firstname : null,
                        'lastname' => $update->user->lastname ? $update->user->lastname : null,
                        'nickname' => $update->user->nickname ? $update->user->nickname : null,
                        'subject' => $update->subject,
                        'content' => $update->content,
                        'permission' => $update->permission,
                        'formatted_created_at' => $update->created_at->format('F j, Y \a\t g:i a - l'),
                        'max_page' => ceil(($update->count()) / 10)
                    ];
                });
        } else if ($filter === 'other') {
            $results = Update::with('user')
                ->where('user_id', '!=', $user->id)
                ->where('permission', 'approved')
                ->orderBy('created_at', $order === 'newest' ? 'desc' : 'asc')
                ->skip($page)
                ->take($per_page)
                ->get()
                ->map(function ($update) {
                    return [
                        'id' => $update->id,
                        'user_id' => $update->user_id,
                        'firstname' => $update->user->firstname ? $update->user->firstname : null,
                        'lastname' => $update->user->lastname ? $update->user->lastname : null,
                        'nickname' => $update->user->nickname ? $update->user->nickname : null,
                        'subject' => $update->subject,
                        'content' => $update->content,
                        'permission' => $update->permission,
                        'formatted_created_at' => $update->created_at->format('F j, Y \a\t g:i a - l'),
                        'max_page' => ceil(($update->count()) / 10)
                    ];
                });
        }
        return response()->json(['result' => $results]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $update = new Update;
        $update->subject = $request->subject;
        $update->content = $request->content;
        $update->user_id = $user->id;
        $update->permission = $user->type === 'admin' ? "approved" : "disapproved";
        $update->updates_blob_id = $request->updates_blob_id;
        $update->type = $request->type;
        $update->save();

        if ($user->type === 'admin') {
            $this->sendNotification($request->subject, $request->content);
        }

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
        $update->updates_blob_id = $request->updates_blob_id;
        $update->type = $request->type;
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

        if (request()->permission == 'approved') {
            $this->sendNotification($update->subject, $update->content);
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

        UpdatesBlobs::where('updates_blob_id', $update->updates_blob_id)->delete();
        $update->delete();
        return response()->json(['message' => 'Update(s) successfully deleted']);
    }

    private function sendNotification($subject = null, $content = null) {
        $notification = new Notification();
        $notification->title = 'New Update Notification';
        $notification->notification_object = json_encode([
            'todo_id' => null,
            'title' => 'New Update Posted A While Ago',
            'content' => "There is a new update that has been posted, please check it out.",
        ]);
        $notification->user_id = null;
        $notification->save();

        try {
            $users = User::whereNotNull("email_verified_at")->get()->shuffle();
            $recipientEmails = $users->pluck('email')->toArray();

            $subjectArray = explode('=>', $subject, 2);

            $subjectValue = trim(html_entity_decode(strip_tags($subjectArray[1]))); // Get the second element and remove HTML tags
            $contentValue = trim(html_entity_decode(strip_tags($content))); // Get the second element and remove HTML tags

            $dataToSend = [
                'subject' => $subjectValue,
                'content' => $contentValue,
            ];

            Mail::bcc('joshua.algadipe@student.passerellesnumeriques.org')->send(new UpdateNotificationEmail($dataToSend));
        } catch (Exception $e) {
            // Handle exception if needed
        }
    }
}
