<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GroupChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class GroupChatImageController extends Controller
{
    public function getGroupImage($id)
    {
        $result = GroupChat::where("id", $id)->first();

        if ($result) {
            $groupImage = $result->group_image;

            if ($groupImage) {
                $imageData = base64_decode($groupImage); // Convert base64-encoded string to binary data
                $imageType = 'image/jpeg'; // Set the appropriate image MIME type

                return response($imageData)
                    ->header('Content-Type', $imageType)
                    ->header('Content-Disposition', 'inline');
            }
        }
        return response()->json(['message' => 'Image not found.'], 404);
    }

    public function updateGroupImage(Request $request)
    {
        $user = Auth::user();

        // Check if the user is authenticated
        if ($user->type !== 'admin' && ($user === null || intval($user->id) !== intval($request->user_id))) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401, ['Content-Type' => 'application/json; charset=utf-8']);
        }

        // Validate the uploaded file
        $request->validate([
            'image' => 'required|image|max:50000', // Assuming maximum 50MB file size limit
        ]);

        // Retrieve the uploaded file
        $image = $request->file('image');

        // Read the contents of the file and convert it to a blob
        $imageBlob = file_get_contents($image->getPathname());
        $imageBlob = base64_encode($imageBlob);

        if ($user->type === 'admin' && $request->input('role') === 'admin') {
            $roomId = $request->groupId;
            $groupImage = GroupChat::find($roomId);
            $groupImage->group_image = $imageBlob;
            $groupImage->save();
        } else {
            $groupImage = GroupChat::find($request->id);
            $groupImage->group_image = $imageBlob;
            $groupImage->save();
        }

        // Redirect or perform additional actions as needed
        return response()->json([
            'success' => true,
            'message' => 'Image updated successfully.',
        ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
    }

}
