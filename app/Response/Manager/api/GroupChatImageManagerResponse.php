<?php

namespace App\Response\Manager\api;

use App\Models\GroupChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupChatImageManagerResponse
{
    public function getGroupImage($id)
    {
        $result = GroupChat::where("id", $id)->first();
        if ($result) {
            $groupImage = $result->group_image;

            if ($groupImage) {
                $imageData = base64_decode($groupImage);
                $imageType = 'image/jpeg';

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
        if ($user->type !== 'admin' && ($user === null || intval($user->id) !== intval($request->user_id))) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401, ['Content-Type' => 'application/json; charset=utf-8']);
        }

        $request->validate([
            'image' => 'required|image|max:50000',
        ]);

        $image = $request->file('image');
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

        return response()->json([
            'success' => true,
            'message' => 'Image updated successfully.',
        ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
    }
}
