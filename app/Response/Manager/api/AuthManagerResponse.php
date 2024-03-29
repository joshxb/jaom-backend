<?php

namespace App\Response\Manager\api;

use App\Models\Configuration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthManagerResponse
{
    public static $configurationId = 2023;

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'phone', 'password']);

        $user = null;

        $configure = Configuration::find(self::$configurationId);
        if (!$configure) {
            return response()->json(['message' => 'Configuration not found'], 404);
        }

        $login_credentials = json_decode($configure->login_credentials, true);
        $trueCredentials = [];

        foreach ($login_credentials as $key => $value) {
            if ($value === true) {
                $trueCredentials[] = $key;
            }
        }

        if (isset($credentials['email'])) {
            if ($trueCredentials[0] === 'phone') {
                return response()->json(['message' => 'Email address is not supported for login credentials'], 401);
            }
            $user = User::where('email', $credentials['email'])->first();
        } else if (isset($credentials['phone'])) {
            if ($trueCredentials[0] === 'email') {
                return response()->json(['message' => 'Phone number is not supported for login credentials'], 401);
            }
            $user = User::where('phone', $credentials['phone'])->first();
        }

        if ($user) {
            $user = null;
            if (isset($credentials['email'])) {
                $user = User::where('email', $credentials['email'])
                    ->whereNotNull('email_verified_at')
                    ->first();
            } else if (isset($credentials['phone'])) {
                $user = User::where('phone', $credentials['phone'])
                    ->whereNotNull('email_verified_at')
                    ->first();
            }

            if ($user) {
                if (Auth::attempt($credentials)) {
                    $user = Auth::user();
                    if ($user->status !== 'active') {
                        $user->status = 'active';
                        $user->save();
                    }

                    $user->updated_at = now();
                    $user->save();

                    $token = $user->createToken('api-token', ['expires_at' => now()->addDay()->toDateTimeString()])->plainTextToken;
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
        $user = $request->user();
        if ($user->status !== 'inactive') {
            $user->status = 'inactive';
            $user->save();
        }

        $user->updated_at = now();
        $user->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function getToken(Request $request)
    {
        $token = $request->header('Authorization');
        return response()->json(['token' => $token]);
    }
}
