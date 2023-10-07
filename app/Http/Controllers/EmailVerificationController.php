<?php

namespace App\Http\Controllers;

use App\Response\Manager\web\EmailVerificationManagerResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    private $emailVerificationManagerResponse;

    public function __construct(
        EmailVerificationManagerResponse $emailVerificationManagerResponse
    ) {
        $this->emailVerificationManagerResponse = $emailVerificationManagerResponse;
    }

    public function verifyEmail(Request $request)
    {
        return $this->emailVerificationManagerResponse->verifyEmail($request);
    }

    public function verifyEmailSent($email, $base)
    {
        return $this->emailVerificationManagerResponse->verifyEmailSent($email, $base);
    }
}
