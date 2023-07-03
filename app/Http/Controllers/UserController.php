<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
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
        $user = User::create($request->all());
        return response()->json(['data' => $user], 201);
    }

    /**
     * Display the specified resource.
     */
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

        // Check if the authenticated user is authorized to update the profile
        if (Auth::user()->id !== $user->id) {
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
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted']);
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

}
