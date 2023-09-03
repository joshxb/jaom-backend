<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigurationController extends Controller
{
    public static $configurationId = 2023;

    public function show()
    {
        $user = Configuration::find(self::$configurationId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['data' => $user]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'donation_info_object' => 'json',
            'contact_details_object' => 'json',
        ]);

        $user = Auth::user();

        if ($user->type != 'admin') {
            return response()->json(['message' => "You don't have permission to modify the configuration."], 404);
        }

        $configuration = Configuration::find(self::$configurationId);

        if (!$configuration) {
            return response()->json(['message' => 'Configuration not found'], 404);
        }

        if ($request->filled('donation_info_object')) {
            $configuration->donation_info_object = $request->input('donation_info_object');
        }

        if ($request->filled('contact_details_object')) {
            $configuration->contact_details_object = $request->input('contact_details_object');
        }

        $configuration->save();
        return response()->json(['message' => 'Configuration updated successfully']);
    }
}
