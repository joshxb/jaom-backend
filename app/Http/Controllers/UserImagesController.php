<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserImagesController extends Controller
{

    // public function uploadUserImage(Request $request)
    // {
    //     // Validate the uploaded file
    //     $request->validate([
    //         'image' => 'required|image|max:50000', // Assuming maximum 50MB file size limit
    //     ]);

    //     // Retrieve the uploaded file
    //     $image = $request->file('image');

    //     // Read the contents of the file and convert it to a blob
    //     $imageBlob = file_get_contents($image->getPathname());
    //     $imageBlob = base64_encode($imageBlob);

    //     // Create a new image record in the database
    //     $userImage = new User;
    //     $userImage->image_blob = $imageBlob;
    //     $userImage->save();

    //     // Redirect or perform additional actions as needed
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Image uploaded successfully.',
    //     ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
    // }

    public function getUserImage()
    {
        $user = Auth::user();
        $userImage = User::find($user->id);

        if ($userImage && $userImage->image_blob) {
            $imageData = base64_decode($userImage->image_blob); // Convert base64-encoded string to binary data
            $imageType = 'image/jpeg'; // Set the appropriate image MIME type

            return response($imageData)
                ->header('Content-Type', $imageType)
                ->header('Content-Disposition', 'inline'); // Set the filename as needed
        }

        return response()->json(['message' => 'Image not found.'], 404);
    }

    public function updateUserImage(Request $request)
    {
        $user = Auth::user();
        // Validate the uploaded file
        $request->validate([
            'image' => 'required|image|max:50000', // Assuming maximum 50MB file size limit
        ]);

        // Retrieve the uploaded file
        $image = $request->file('image');

        // Read the contents of the file and convert it to a blob
        $imageBlob = file_get_contents($image->getPathname());
        $imageBlob = base64_encode($imageBlob);

        // Create a new image record in the database
        $userImage = User::find($user->id);
        $userImage->image_blob = $imageBlob;
        $userImage->save();

        // Redirect or perform additional actions as needed
        return response()->json([
            'success' => true,
            'message' => 'Image updated successfully.',
        ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
    }

    // public function deleteUserImage($user_id)
    // {
    //     $userImage = User::find($user_id);

    //     if (!$userImage) {
    //         return response()->json(['error' => 'Image not found.']);
    //     }

    //     $userImage->delete();
    //     return response()->json(['success' => 'Image deleted successfully.']);
    // }
}
