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

        // Check if the 'phone' key exists in the request
        if ($request->has('phone')) {
            $credentials = $request->only(['email', 'phone', 'password']);
        } else {
            $credentials = $request->only(['email', 'password']);
        }

        // Add an additional check to allow login only if 'email_verified_at' is not null
        $user = \App\Models\User::whereNotNull('email_verified_at')
            ->where(function ($query) use ($credentials) {
                if (isset($credentials['email'])) {
                    $query->where('email', $credentials['email']);
                }
                if (isset($credentials['phone'])) {
                    $query->orWhere('phone', $credentials['phone']);
                }
            })
            ->first();

        if ($user) {
            if (Auth::attempt($credentials)) {
                $token = $user->createToken('api-token', ['expires_at' => now()->addDay()])->plainTextToken;
                return response()->json(['message' => 'Successfully login', 'token' => $token]);
            } else {
                return response()->json(['message' => 'Incorrect credentials'], 401);
            }
        } else {
            return response()->json(['message' => 'Email is not verified, please check your email for verification.'], 401);
        }
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
