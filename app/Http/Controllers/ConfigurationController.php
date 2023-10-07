<?php

namespace App\Http\Controllers;

use App\Response\Manager\api\ConfigureManagerResponse;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    private $configureManagerResponse;

    public function __construct(
        ConfigureManagerResponse $configureManagerResponse
    ) {
        $this->configureManagerResponse = $configureManagerResponse;
    }

    public function show()
    {
        return $this->configureManagerResponse->show();
    }

    public function update(Request $request)
    {
        return $this->configureManagerResponse->update($request);
    }

    public function getTrueLoginCredentials()
    {
        return $this->configureManagerResponse->getTrueLoginCredentials();
    }
}
