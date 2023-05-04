<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $user->update($request->all());
        return response()->json(['data' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
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

        if (!empty($range)) {
            if ($range == 1) {
                $users->orderBy('id')->take(10);
            } elseif ($range > 1) {
                $start = ($range - 1) * 10 + 1;
                $end = $start + 9;
                $users->whereBetween('id', [$start, $end]);
            }
        }

        $users = $users->get();

        return response()->json(['data' => $users]);
    }

}
