<?php

namespace App\Response\Manager\api;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigureManagerResponse
{

    public static $configurationId = 2023;

    public function show()
    {
        $configure = Configuration::find(self::$configurationId);
        if (!$configure) {
            return response()->json(['message' => 'Configuration not found'], 404);
        }

        return response()->json(['data' => $configure]);
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

        if ($request->filled('auto_add_room')) {
            $configuration->auto_add_room = $request->input('auto_add_room');
        }

        if ($request->filled('login_credentials')) {
            $configuration->login_credentials = $request->input('login_credentials');
        }

        if ($request->filled('account_deactivation')) {
            $configuration->account_deactivation = $request->input('account_deactivation');
        }

        $configuration->save();
        return response()->json(['message' => 'Configuration updated successfully']);
    }

    public function getTrueLoginCredentials()
    {
        $configure = Configuration::find(self::$configurationId);
        if (!$configure) {
            return response()->json(['message' => 'Configuration not found'], 404);
        }

        $credentials = json_decode($configure->login_credentials, true);
        $trueCredentials = [];

        foreach ($credentials as $key => $value) {
            if ($value === true) {
                $trueCredentials[] = $key;
            }
        }

        return response()->json(['access_method' => $trueCredentials]);
    }
}
