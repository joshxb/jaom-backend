<?php

namespace App\Response\Manager\api;

use App\Mail\OfferPrayerMail;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class NotificationManagerResponse
{ public function index()
    {
        $notifications = Notification
            ::where('title', "Prayer Offer Request Notification")
            ->orWhere('title', "Donation's Transaction Notification")
            ->orWhere('title', "Newly Bible Quote Sent to Email")
            ->orWhere('title', "Concern & Feedback Notification")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($notifications);
    }

    public function currentIndex()
    {
        $user = auth()->user();
        $notifications = Notification::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhereNull('user_id');
        })
        ->where('created_at', '>=', $user->created_at)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        if ($notifications->isEmpty()) {
            return response()->json([
                'message' => 'No notifications yet!',
            ]);
        }

        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        if (count($request->userIds) > 0) {
            $notifications = collect($request->userIds)->map(function ($user_id) use ($request) {
                $notification = new Notification();

                if ($request->type == "addInRoomNotification") {
                    $notification->title = 'Newly Added Member From Room';
                    $notification->notification_object = json_encode([
                        'todo_id' => null,
                        'title' => $request->roomName,
                        'content' => "You have been added from the group chat by the owner",
                    ]);
                } elseif ($request->type == "removeInRoomNotification") {
                    $notification->title = 'Room Membership Notice:';
                    $notification->notification_object = json_encode([
                        'todo_id' => null,
                        'title' => $request->roomName,
                        'content' => "You have been removed from the group chat by the owner",
                    ]);
                } elseif ($request->type == "leftInRoomNotification") {
                    $notification->title = 'Room Deletion Notice: The Room ha Been Disbanded';
                    $notification->notification_object = json_encode([
                        'todo_id' => null,
                        'title' => $request->roomName,
                        'content' => "The owner's departure led to the removal of the chat.",
                    ]);
                } elseif ($request->type == "leftInRoomNotification2") {
                    $notification->title = 'Group Notification: One Member Left from the Group';
                    $notification->notification_object = json_encode([
                        'todo_id' => null,
                        'title' => $request->name,
                        'content' => 'A member has left from the ' . $request->roomName . ' group',
                    ]);
                } elseif ($request->type == "removeInDeletedRoomNotification") {
                    $notification->title = 'Room Deletion Notice: The Room has Been Disbanded';
                    $notification->notification_object = json_encode([
                        'todo_id' => null,
                        'title' => $request->roomName,
                        'content' => "Following the owner's decision to delete the group, all members have been removed.",
                    ]);
                } else if ($request->type == "newMemberChatNotification") {
                    $notification->title = $request->title;
                    $notification->notification_object = json_encode([
                        'todo_id' => null,
                        'title' => $request->name,
                        'content' => $request->content,
                    ]);
                } else if ($request->type == "successAddDonationNotification") {
                    $notification->title = "Donation's Transaction Notification";
                    $notification->notification_object = json_encode([
                        'todo_id' => null,
                        'title' => "Hello there, " . $request->name . ",",
                        'content' => "We have successfully processed and recorded your donation's transaction to the ministry. Thank you so much for your generous support to
                        our ministry and we appreciate this all a lot from you.",
                    ]);
                } else if ($request->type == "successAddFeedbackNotification") {
                    $notification->title = "Concern & Feedback Notification";
                    $notification->notification_object = json_encode([
                        'todo_id' => null,
                        'title' => "Hello there, " . $request->name . ",",
                        'content' => "Your concern or feedback to the ministry has been successfully raised. Please await further responses, thank you so much!",
                    ]);
                } else if ($request->type == "successAddOfferNotification") {
                    $notification->title = "Prayer Offer Request Notification";
                    $notification->notification_object = json_encode([
                        'todo_id' => null,
                        'title' => "Hi " . Auth::user()->firstname . " " .Auth::user()->lastname . ",",
                        'content' => "Your prayer offer was now successfully sent to the ministry."
                    ]);

                    $userData = [
                        'name' =>  ucwords(Auth::user()->firstname . " " .Auth::user()->lastname),
                        'name2' => ucwords($request->name),
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'address' => $request->address,
                        'offer' => $request->offer
                    ];

                    Mail::to($userData['email'])->send(new OfferPrayerMail($userData));
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
        Notification::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Notifications are cleared successfully!',
        ]);
    }
}
