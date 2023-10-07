<?php

namespace App\Response\Manager\api;

use Artisan;
use Illuminate\Http\Request;

class MigrationManagerResponse
{
    public function migrate(Request $request)
    {
        $request->validate([
            'key' => 'required'
        ]);

        $pass = 'jaomconnect1xtyuiouy895603api';
        $key = $request->key;

        if ($key === $pass) {
            Artisan::call('migrate', [
                '--force' => true
            ]);
            return response()->json(['message' => 'Migration completed successfully']);
        } else {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }
    }
}
