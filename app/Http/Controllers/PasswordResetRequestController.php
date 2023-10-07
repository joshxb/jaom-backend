<?php

namespace App\Http\Controllers;

use App\Response\Manager\web\PasswordResetRequestManagerResponse;
use Illuminate\Http\Request;

class PasswordResetRequestController extends Controller
{
    private $passwordResetRequestManagerResponse;

    public function __construct(
        PasswordResetRequestManagerResponse $passwordResetRequestManagerResponse
    ) {
        $this->passwordResetRequestManagerResponse = $passwordResetRequestManagerResponse;
    }

    public function sendRequest(Request $request)
    {
        return $this->passwordResetRequestManagerResponse->sendRequest($request);
    }

    public function resetPassword(Request $request) {
        return $this->passwordResetRequestManagerResponse->resetPassword($request);
    }
}
