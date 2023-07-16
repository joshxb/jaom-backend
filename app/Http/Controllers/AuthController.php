<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'phone', 'password']);

        if (Auth::attempt($credentials)) {
            $user = $request->user();
            $token = $user->createToken('api-token', ['expires_at' => now()->addDay()])->plainTextToken;
            return response()->json(['token' => $token]);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function checkToken(Request $request)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            return response()->json(['valid' => true]);
        }

        return response()->json(['message' => 'User not authenticated or token expired.'], 401);
    }
}
