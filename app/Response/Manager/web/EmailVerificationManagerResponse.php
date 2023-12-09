<?php

namespace App\Response\Manager\web;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;

class EmailVerificationManagerResponse
{
    public function verifyEmail(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $base = $request->input('base');
        $verify = $request->input('verify');
        $email_change = $request->input('email_change');
        $previous_email = $request->input('previous_email');

        $userData = [
            'name' =>  $name,
            'email' => $email,
            'base' => $base,
            'verify' => $verify,
            'email_change' => $email_change,
            'previous_email' => $previous_email
        ];

        Mail::to($userData['email'])->send(new VerificationEmail($userData));
        return response()->json([
            'success' => true
        ], 200);
    }

    public function verifyEmailSent($email, $base)
    {
        $local = env('F_LOCAL_BASE_URL');
        $deploy = env('F_DEPLOYMENT_BASE_URL');

        $url = ($base == "l") ? $local : (($base == "d") ? $deploy : $local);

        if (request()->input('email_change')) {
            $user = User::where('email', request()->input('previous_email'))->first();
            if ($user) {
                User::where('email', request()->input('previous_email'))->update([
                    "email" => $email,
                    "email_verified_at" => now()
                ]);
                return redirect($url."/email-verification-success");
            } else {
                return redirect($url."/email-verification-exist");
            }
        } else {
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
}
