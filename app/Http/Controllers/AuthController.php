<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'phone', 'password']);

        $user = null;

        if (isset($credentials['email'])) {
            $user = User::where('email', $credentials['email'])->first();
        }else if (isset($credentials['phone'])) {
            $user = User::where('phone', $credentials['phone'])->first();
        }

        if ($user) {
            $user = null;
            if (isset($credentials['email'])) {
                $user = User::where('email', $credentials['email'])
                    ->whereNotNull('email_verified_at')
                    ->first();
            }else if (isset($credentials['phone'])) {
                $user = User::where('phone', $credentials['phone'])
                    ->whereNotNull('email_verified_at')
                    ->first();
            }

            if ($user) {
                if (Auth::attempt($credentials)) {
                    $token = Auth::user()->createToken('api-token', ['expires_at' => now()->addDay()->toDateTimeString()])->plainTextToken;
                    return response()->json(['message' => 'Successfully login', 'token' => $token]);
                } else {
                    return response()->json(['message' => 'Incorrect credentials'], 401);
                }
            } else {
                return response()->json(['message' => 'Email is not verified, please check your email for verification.'], 401);
            }
        } else {
            return response()->json(['message' => 'Incorrect credentials'], 401);
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
