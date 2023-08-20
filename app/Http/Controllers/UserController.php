<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DonateTransactions;
use App\Models\Feedback;
use App\Models\GroupChat;
use App\Models\GroupMessage;
use App\Models\GroupUser;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Offer;
use App\Models\Todo;
use App\Models\Update;
use App\Models\User;
use App\Models\UserHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\Conversation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\BibleGeneratorController;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $pagination = 10;
        $users = User::paginate($pagination);
        return response()->json([$users]);
    }

    public function userCounts()
    {
        $userCount = User::count();
        return response()->json(['user_count' => $userCount]);
    }

    public function countUsersByStatus()
    {
        $activeUserCount = User::where('status', 'active')->count();
        $inactiveUserCount = User::where('status', 'inactive')->count();

        return response()->json([
            'active_user_count' => $activeUserCount,
            'inactive_user_count' => $inactiveUserCount
        ]);
    }

    public function userRange(Request $request)
    {
        $range = $request->input('range');
        $users = User::all();

        if ($range == 1) {
            $users = User::orderBy('id')->take(10)->get();
        } elseif ($range > 1) {
            $start = ($range - 1) * 10 + 1;
            $end = $start + 9;
            $users = User::whereBetween('id', [$start, $end])->get();
        }

        return response()->json(['data' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:15|unique:users,phone',
            'password' => 'required|string|min:5',
            'type' => 'nullable|string',
            'image' => 'nullable|string',
            'nickname' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0|max:150',
            'visibility' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $validatedData['firstname'] = ucwords($validatedData['firstname']);
        $validatedData['lastname'] = ucwords($validatedData['lastname']);
        $validatedData['type'] = 'local';
        $validatedData['visibility'] = 'visible';
        $validatedData['status'] = 'active';

        // Set default value for 'nickname' if not provided in the request
        $validatedData['nickname'] = '~!@#$%^&*()-=_+[]{}|;:,.<>?false';

        // Format the phone number to Philippine format
        $formattedPhone = '0' . substr(preg_replace('/[^0-9]/', '', $validatedData['phone']), -10);
        $validatedData['phone'] = $formattedPhone;

        // Hash the password before storing it using bcrypt()
        $hashedPassword = bcrypt($validatedData['password']);
        $validatedData['password'] = $hashedPassword;

        $validatedData['email_verified_at'] = null;

        $base = $request->input('base', "l");
        $requestData = [
            'email' => $validatedData['email'],
            'name' => ucwords($validatedData['firstname'] . " " . $validatedData['lastname']),
            'base' => $base
        ];

        // Create a new instance of Request with the data
        $request = Request::create('/verify_email/' . $validatedData['email'], 'POST', $requestData);

        // Dispatch the new request to call the verifyEmail method with the user's email and name
        $response = app()->handle($request);

        if ($response->getStatusCode() === 200) {
            $user = User::create($validatedData);
            return response()->json(['data' => $user], 201);
        } else {
            return response()->json(['error' => 'Email verification failed'], 400);
        }
    }

    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $userImage = User::find($user->id);
        if ($userImage && $userImage->image_blob) {
            //     $imageData = base64_decode($userImage->image_blob); // Convert base64-encoded string to binary data
            //     $imageType = 'image/jpeg'; // Set the appropriate image MIME type

            //     return response($imageData)
            //         ->header('Content-Type', $imageType)
            //         ->header('Content-Disposition', 'inline'); // Set the filename as needed
        }

        return response()->json(['data' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($request->input('role') == 'admin') {
            //allow admin to configure update
        }
        // Check if the authenticated user is authorized to update the profile
        else if (Auth::user()->id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Update the user data
        $requestData = $request->all();

        if (isset($requestData['password'])) {
            $requestData['password'] = Hash::make($requestData['password']);
        }

        $user->update($requestData);

        return response()->json([
            'data' => $user,
            'message' => 'Data updated successfully!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($request->input('role') && $request->input('role') != 'admin') {
            return response()->json(['message' => "You don't have permission to remove a user."], 401);
        } else if ($request->input('role') == '' && Auth::user()->id !== $id) {
            return response()->json(['message' => 'Unauthorized for deleting other account'], 401);
        }

        Conversation::where('user1_id', $id)
            ->orWhere('user2_id', $id)
            ->delete();

        DonateTransactions::where("user_id", $id)
            ->delete();

        Feedback::where("user_id", $id)->delete();
        GroupChat::where("user_id", $id)->delete();
        GroupMessage::where("user_id", $id)->delete();
        GroupUser::where("user_id", $id)->delete();
        Message::where("sender_id", $id)->delete();
        Notification::where("user_id", $id)->delete();
        Offer::where("user_id", $id)->delete();
        Todo::where("user_id", $id)->delete();
        Update::where("user_id", $id)->delete();
        UserHistory::where("user_id", $id)->delete();

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function searchUsers(Request $request)
    {
        $range = $request->input('range');
        $search = $request->input('search');

        $users = User::query();

        if (!empty($search)) {
            $users->where(function ($query) use ($search) {
                $query->where(DB::raw("concat(firstname, ' ', lastname)"), 'like', '%' . $search . '%');
            });
        }

        // Add the 'where' condition for visibility
        $users->where('visibility', 'visible');

        $total_users = $users->count();
        if (!empty($range)) {
            if ($range == 1) {
                $users->orderBy('id')->take(10);
            } elseif ($range > 1) {
                $start = ($range - 1) * 10 + 1;
                $users->orderBy('id')->skip($start - 1)->take(10);
            }
        }

        $users = $users->get();
        $max_range = $total_users > 0 ? ceil($total_users / 10) : 0;

        return response()->json([
            'data' => $users,
            'max_range' => $max_range,
        ]);
    }

    public function searchUsersWithExceptCurrentGroup(Request $request)
    {
        $range = $request->input('range');
        $search = $request->input('search');

        $groupUsers = GroupUser::where("group_id", $request->group_id)->get();

        $users = User::where(function ($query) use ($search, $groupUsers) {
            foreach ($groupUsers as $groupUser) {
                // Access individual groupUser properties
                $userId = $groupUser->user_id;
                $query->where("id", "!=", $userId);
                $query->where(DB::raw("concat(firstname, ' ', lastname)"), 'like', '%' . $search . '%');
            }
        });

        // Add the 'where' condition for visibility
        $users->where('visibility', 'visible');

        $total_users = $users->count();

        if (!empty($range)) {
            if ($range == 1) {
                $users->orderBy('id')->take(10);
            } elseif ($range > 1) {
                $start = ($range - 1) * 10;
                $users->orderBy('id')->skip($start)->take(10);
            }
        }

        $users = $users->get();
        $max_range = $total_users > 0 ? ceil($total_users / 10) : 0;

        return response()->json([
            'data' => $users,
            'max_range' => $max_range,
            'groupUsers' => $groupUsers,
        ]);
    }

    public function searchUsersWithCurrentGroup(Request $request)
    {
        $range = $request->input('range');
        $search = $request->input('search');

        $groupUsers = GroupUser::where("group_id", $request->group_id)->get();

        $userIds = $groupUsers->pluck('user_id')->toArray();

        $users = User::whereIn("id", $userIds)
            ->where(DB::raw("concat(firstname, ' ', lastname)"), 'like', '%' . $search . '%')
            ->where('visibility', 'visible'); // Add the 'where' condition for visibility

        $total_users = $users->count();

        if (!empty($range)) {
            if ($range == 1) {
                $users->orderBy('id')->take(10);
            } elseif ($range > 1) {
                $start = ($range - 1) * 10;
                $users->orderBy('id')->skip($start)->take(10);
            }
        }

        $users = $users->get();
        $max_range = $total_users > 0 ? ceil($total_users / 10) : 0;

        return response()->json([
            'data' => $users,
            'max_range' => $max_range,
            'groupUsers' => $groupUsers,
        ]);
    }

    public function removeNotVerifiedEmail()
    {
        $bibleGeneratorController = App::make(BibleGeneratorController::class);

        $usersNotVerified = User::whereNull("email_verified_at")->get();

        if ($usersNotVerified->isEmpty()) {
            $bibleGeneratorController->generateBibleQuote();
            return "No users found with unverified email addresses.";
        }

        foreach ($usersNotVerified as $user) {
            $user->delete();
        }

        $bibleGeneratorController->generateBibleQuote();

        return count($usersNotVerified) . " user(s) with unverified email addresses have been removed.";
    }
}
