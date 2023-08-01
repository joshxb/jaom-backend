<?php

namespace App\Http\Controllers;

use Artisan;
use Illuminate\Http\Request;

class MigrationController extends Controller
{
    public function migrate(Request $request)
    {
        $request->validate([
            'key' => 'required'
        ]);

        $pass = 'jaomconnect1xtyuiouy895603api';
        $key = $request->key;

        // Check if the key matches the authorized key
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
