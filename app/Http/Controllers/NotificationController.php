<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function index()
    {
        $notifications = Notification::all();

        return response()->json([
            'data' => $notifications,
        ]);
    }

    public function currentIndex()
    {
        $user = auth()->user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($notifications->isEmpty()) {
            return response()->json([
                'message' => 'No notifications yet!',
            ]);
        }

        return response()->json([
            'data' => $notifications,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (count($request->userIds) == 0) {
            $request->validate([
                'title' => 'required|string',
                'notification_object' => 'required|string',
            ]);

            $notification = new Notification();
            $notification->title = $request->title;
            $notification->notification_object = $request->notification_object;
            $notification->user_id = $request->user_id;
            $notification->save();

            return response()->json([
                'data' => $notification,
                'message' => 'Notification created successfully!',
            ]);

        } else {
            $notifications = collect($request->userIds)->map(function ($user_id) use ($request) {
                $notification = new Notification();

                if ($request->type == "addInRoomNotification") {
                    $notification->title = 'Add Member From Room Notification';
                    $notification->notification_object = json_encode([
                        'todo_id' => null,
                        'title' => $request->roomName,
                        'content' => "You have been added from the group chat by the owner",
                    ]);
                } elseif ($request->type == "removeInRoomNotification") {
                    $notification->title = 'Remove Member From Room Notification';
                    $notification->notification_object = json_encode([
                        'todo_id' => null,
                        'title' => $request->roomName,
                        'content' => "You have been removed from the group chat by the owner",
                    ]);
                } elseif ($request->type == "removeInDeletedRoomNotification") {
                    $notification->title = 'The Room has Been Deleted Notification';
                    $notification->notification_object = json_encode([
                        'todo_id' => null,
                        'title' => $request->roomName,
                        'content' => "You have been removed because the room has been deleted by the owner",
                    ]);
                }

                $notification->user_id = $user_id;
                $notification->save();

                return $notification;
            });

            return response()->json([
                'data' => $notifications,
                'message' => 'Notification created successfully!',
            ]);
        }
    }


    public function show($id)
    {
        $notification = Notification::findOrFail($id);

        return response()->json([
            'data' => $notification,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'notification_object' => 'required|string',
        ]);

        $notification = Notification::findOrFail($id);
        $notification->update($request->all());

        return response()->json([
            'data' => $notification,
            'message' => 'Notification updated successfully!',
        ]);
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully!',
        ]);
    }

    public function destroyAll()
    {
        $user = auth()->user();
        $notifications = Notification::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Notifications are cleared successfully!',
        ]);
    }
}
