<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;


class EmailVerificationController extends Controller
{
    public function verifyEmail($email)
    {
        // Perform the email verification logic here.
        // You can mark the email as verified in your database or perform any other actions required.

        // For this example, we'll just send the verification email again as you did in the original code.

        $userData = [
            'name' => 'Joshua Algadipe',
            'email' => $email,
            // Add other user data here if needed
        ];

        Mail::to($userData['email'])->send(new VerificationEmail($userData));

        // Redirect the user to a page indicating that their email verification link has been sent.
        return response()->json([
            'success' => true
        ], 200);
    }
}
