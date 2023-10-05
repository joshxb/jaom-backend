<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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
use App\Models\Conversation;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\BibleGeneratorController;
use App\Models\Configuration;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $pagination = 10;
        $users = User::paginate($pagination);

        // Manually create an array to include pagination details
        $data = [
            'current_page' => $users->currentPage(),
            'data' => UserResource::collection($users),
            'first_page_url' => $users->url(1),
            'from' => $users->firstItem(),
            'last_page' => $users->lastPage(),
            'last_page_url' => $users->url($users->lastPage()),
            'next_page_url' => $users->nextPageUrl(),
            'path' => $users->path(),
            'per_page' => $users->perPage(),
            'prev_page_url' => $users->previousPageUrl(),
            'to' => $users->lastItem(),
            'total' => $users->total(),
        ];

        return response()->json([$data]);
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

    public function adminAccessUsers()
    {
        if (Auth::user()->type != 'admin') {
            return response()->json(['message' => "Unauthorized you don't have a permission!"], 401);
        }

        $users = User::where('id', '!=', Auth::user()->id)->where('type', 'admin')->get();
        return response()->json(['data' => $users]);
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

        return response()->json(['data' => new UserResource($user)]);
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

        if (Auth::user()->type == 'admin' && request()->has('role') && request()->input('role') == 'admin') {
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

        $requestData['updated_at'] = now();

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
        $groupUsers = GroupUser::where("group_id", $request->group_id)->get()->pluck('user_id')->toArray();

        $users = User::query();

        if (!empty($search)) {
            $users->where(function ($query) use ($search) {
                $query->where(DB::raw("concat(firstname, ' ', lastname)"), 'like', '%' . $search . '%');
            });
        }

        $users->whereNotIn('id', $groupUsers);

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
        $usersNotVerified = User::whereNull("email_verified_at")->get();

        ////////////////// extra services //////////////////////

        // bible generator
        $bibleGeneratorController = App::make(BibleGeneratorController::class);
        if ($usersNotVerified->isEmpty()) {
            $bibleGeneratorController->generateBibleQuote();
            return "No users found with unverified email addresses.";
        }

        foreach ($usersNotVerified as $user) {
            $user->delete();
        }

        $bibleGeneratorController->generateBibleQuote();

        // account deactivation
        $userWithAccountDeactivation = Configuration::where('id', 2023)->first();

        $period = '';
        if ($userWithAccountDeactivation) {
            $accountDeactivation = json_decode($userWithAccountDeactivation->account_deactivation, true);

            if (is_array($accountDeactivation)) {
                foreach ($accountDeactivation as $key => $value) {
                    if ($value === true) {
                        $period = $key;
                    }
                }
            }
        }

        $periodNumber = $period == '>3' ? 4 : $period;
        $usersToDeactivate = User::whereRaw("DATEDIFF(NOW(), updated_at) > ?", [$periodNumber * 365])
            ->where('type', '!=', 'admin')
            ->get();

        foreach ($usersToDeactivate as $user) {
            Conversation::where('user1_id', $user->id)
                ->orWhere('user2_id', $user->id)
                ->delete();
            DonateTransactions::where("user_id", $user->id)
                ->delete();
            Feedback::where("user_id", $user->id)->delete();
            GroupChat::where("user_id", $user->id)->delete();
            GroupMessage::where("user_id", $user->id)->delete();
            GroupUser::where("user_id", $user->id)->delete();
            Message::where("sender_id", $user->id)->delete();
            Notification::where("user_id", $user->id)->delete();
            Offer::where("user_id", $user->id)->delete();
            Todo::where("user_id", $user->id)->delete();
            Update::where("user_id", $user->id)->delete();
            UserHistory::where("user_id", $user->id)->delete();
            $user->delete();
        }

        return count($usersNotVerified) . " user(s) with unverified email addresses have been removed.";
    }
}
