<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Response\Manager\api\AuthManagerResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $authManagerResponse;

    public function __construct(
        AuthManagerResponse $authManagerResponse
    ) {
        $this->authManagerResponse = $authManagerResponse;
    }

    public function login(Request $request)
    {
        return $this->authManagerResponse->login($request);
    }

    public function logout(Request $request)
    {
        return $this->authManagerResponse->logout($request);
    }

    public function getToken(Request $request)
    {
        return $this->authManagerResponse->getToken($request);
    }
}
