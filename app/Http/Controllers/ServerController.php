<?php

namespace App\Http\Controllers;

use App\Response\Manager\api\ServerManagerResponse;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    private $serverManagerResponse;

    public function __construct(
        ServerManagerResponse $serverManagerResponse
    ) {
        $this->serverManagerResponse = $serverManagerResponse;
    }

    public function getServerInfo()
    {
        return $this->serverManagerResponse->getServerInfo();
    }
}
