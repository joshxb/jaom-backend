<?php

namespace App\Response\Manager\api;

use App\Models\MessagesBlob;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MessagesBlobManagerResponse
{

    public function getMessagesBlob()
    {
        $result = null;
        //// override parameters
        ///// if key is id - get all ids
        if (request()->input('key') == 'id') {
            $result = MessagesBlob::where("messages_blob_id", request()->messages_blob_id)->pluck('id');
            if ($result) {
                return response()->json([
                    'messages_blob_ids' => $result
                ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
            } else {
                return null;
            }
        } else if (request()->input('key') == 'blob' && request()->input('id')) {
            ///// if key is blob - get blobs data and the id param for combination
            $result = MessagesBlob::where("id", request()->id)->first();
            if ($result) {
                $ss = $result->data_blob;
                if ($ss) {
                    $dataArray = explode('base64,', $ss);
                    $imageData = base64_decode($dataArray[1]);
                    $imageType = $dataArray[0]; // 'data:image/jpeg;'
                    // Remove 'data:' prefix and trailing semicolon
                    $imageType = str_replace('data:', '', $imageType);
                    $imageType = rtrim($imageType, ';');

                    return response($imageData)
                        ->header('Content-Type', $imageType)
                        ->header('Content-Disposition', 'inline');
                } else {
                    return null;
                }
            }
        }
        return response()->json(['message' => 'File not found.'], 404);
    }

    public function store()
    {
        try {
            foreach (request()->data_blob as $value) {
                MessagesBlob::create([
                    'messages_blob_id' => request()->messages_blob_id,
                    'data_blob' => $value,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Messages blob created successfully.',
            ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Not found exception'], 404);
        }
    }
}

