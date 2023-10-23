<?php

namespace App\Response\Manager\web;

use App\Mail\PasswordResetEmail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetRequestManagerResponse
{
    public function sendRequest(Request $request)
    {
        $email = $request->input('email');
        $base = $request->input('base');

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Email account does not exist'
            ], 400);
        }

        $user = User::where('email', $request->email)->whereNotNull('email_verified_at')->first();
        if (!$user) {
            return response()->json([
                'message' => 'Your request cannot be processed because the email address is not verified. Please check your previous email.'
            ], 400);
        }

        $token = Password::getRepository()->create($user);
        $userData = [
            'email' => $email,
            'base' => $base,
            'token' => $token
        ];
        Mail::to($userData['email'])->send(new PasswordResetEmail($userData));

        return response()->json([
            'status' => 'success'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
                event(new PasswordReset($user));
            }
        );

        return response()->json([
            'status' => 'success'
        ], 200);
    }
}
