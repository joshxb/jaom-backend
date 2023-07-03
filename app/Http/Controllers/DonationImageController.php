<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DonateTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationImageController extends Controller
{
    public function getScreenShot($id)
    {
        $result = DonateTransactions::where("id", $id)->first();

        if ($result) {
            $ss = $result->screenshot_img;

            if ($ss) {
                $imageData = base64_decode($ss); // Convert base64-encoded string to binary data
                $imageType = 'image/jpeg'; // Set the appropriate image MIME type

                return response($imageData)
                    ->header('Content-Type', $imageType)
                    ->header('Content-Disposition', 'inline');
            }
        }
        return response()->json(['message' => 'Image not found.'], 404);
    }

    public function updateDonationSS(Request $request, $id)
    {
        try {
            $transac = DonateTransactions::findOrFail($id);
            // Validate the uploaded file
            $request->validate([
                'screenshot_img' => 'required|image', // Assuming maximum 50MB file size limit
            ]);

            if (Auth::user()->id !== $transac->user_id) {
                return response()->json(['message' => 'Permission denied'], 401);
            }

            // Retrieve the uploaded file
            $image = $request->file('screenshot_img');

            // Read the contents of the file and convert it to a blob
            $imageBlob = file_get_contents($image->getPathname());
            $imageBlob = base64_encode($imageBlob);

            // Create a new image record in the database
            $ss = DonateTransactions::find($id);
            $ss->screenshot_img = $imageBlob;
            $ss->save();

            // Redirect or perform additional actions as needed
            return response()->json([
                'success' => true,
                'message' => 'Image updated successfully.',
            ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
        } catch (ModelNotFoundException $exception) {
            // If the model is not found, throw an error response.
            return response()->json(['error' => 'Not found exception'], 404);
        }
    }
}
