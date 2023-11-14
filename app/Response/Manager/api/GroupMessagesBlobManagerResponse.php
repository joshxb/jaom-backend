<?php

namespace App\Response\Manager\api;

use App\Models\GroupMessagesBlob;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GroupMessagesBlobManagerResponse
{

    public function getGroupMessagesBlob()
    {
        $result = null;
        //// override parameters
        ///// if key is info - get all info
        if (request()->input('key') == 'info') {
            $results = GroupMessagesBlob::where("group_messages_blob_id", request()->group_messages_blob_id)->get(['id', 'file_name']);
            if ($results->isNotEmpty()) {
                $ids = $results->pluck('id')->toArray();
                $fileNames = $results->pluck('file_name')->toArray();

                return response()->json([
                    'group_messages_blob_ids' => $ids,
                    'file_names' => $fileNames,
                ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
            } else {
                return response()->json([
                    'group_messages_blob_ids' => [],
                    'file_names' => [],
                ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
            }
        } else if (request()->input('key') == 'blob' && request()->input('id')) {
            ///// if key is blob - get blobs data and the id param for combination
            $result = GroupMessagesBlob::where("id", request()->id)->first();
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
            $dataBlobs = request()->data_blob;
            $fileNames = request()->file_name;

            foreach ($dataBlobs as $index => $dataBlob) {
                GroupMessagesBlob::create([
                    'group_messages_blob_id' => request()->group_messages_blob_id,
                    'data_blob' => $dataBlob,
                    'file_name' => $fileNames[$index] ?? null,
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

