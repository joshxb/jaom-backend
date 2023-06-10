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
        $notifications = Notification::where('user_id', $user->id)->get();

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
        $request->validate([
            'title' => 'required|string',
            'notification_object' => 'required|string',
        ]);

        $notification = new Notification();
        $notification->title = $request->title;
        $notification->notification_object = $request->notification_object;
        $notification->user_id = auth()->user()->id;
        $notification->save();

        return response()->json([
            'data' => $notification,
            'message' => 'Notification created successfully!',
        ]);
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
