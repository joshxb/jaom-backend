<?php

namespace App\Http\Controllers;

use App\Response\Manager\api\MigrationManagerResponse;
use Artisan;
use Illuminate\Http\Request;

class MigrationController extends Controller
{
    private $migrationManagerResponse;

    public function __construct(
        MigrationManagerResponse $migrationManagerResponse
    ) {
        $this->migrationManagerResponse = $migrationManagerResponse;
    }

    public function migrate(Request $request)
    {
        return $this->migrationManagerResponse->migrate($request);
    }
}
