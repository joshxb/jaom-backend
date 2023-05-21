<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function getToken(Request $request)
    {
        // Get the token from the request headers
        $token = $request->header('Authorization');

        return response()->json(['token' => $token]);
    }
}
