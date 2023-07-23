<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;


class EmailVerificationController extends Controller
{
    public function verifyEmail(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        //values : local = l, deployment = d
        $base = $request->input('base');

        $userData = [
            'name' =>  $name,
            'email' => $email,
            'base' => $base
        ];

        Mail::to($userData['email'])->send(new VerificationEmail($userData));

        // Redirect the user to a page indicating that their email verification link has been sent.
        return response()->json([
            'success' => true
        ], 200);
    }

    public function verifyEmailSent($email, $base)
    {
        $local = env('F_LOCAL_BASE_URL');
        $deploy = env('F_DEPLOYMENT_BASE_URL');

        $url = ($base == "l") ? $local : (($base == "d") ? $deploy : $local);

        $user = User::where('email', $email)->where('email_verified_at', null)->first();
        if ($user) {
            User::where('email', $email)->update([
                "email_verified_at" => now()
            ]);
            return redirect($url."/email-verification-success");
        } else {
            return redirect($url."/email-verification-exist");
        }
    }
}
