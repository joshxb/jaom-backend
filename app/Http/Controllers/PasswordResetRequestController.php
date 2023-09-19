<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PasswordResetRequestController extends Controller
{
    public function sendRequest(Request $request)
    {
        $email = $request->input('email');
        $base = $request->input('base');
        $token = $request->input('token');

        $userData = [
            'email' => $email,
            'base' => $base,
            'token' => $token
        ];

        Mail::to($userData['email'])->send(new PasswordResetEmail($userData));

        // Redirect the user to a page indicating that their email verification link has been sent.
        return response()->json([
            'status' => 'success'
        ], 200);
    }
}
