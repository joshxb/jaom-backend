<?php

namespace App\Response\Manager\api;

use App\Models\UpdatesBlobs;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdatesBlobManagerResponse
{
    public function store()
    {
        try {
            $dataBlobs = request()->data_blob;
            $fileNames = request()->file_name;

            foreach ($dataBlobs as $index => $dataBlob) {
                UpdatesBlobs::insert([
                    'updates_blob_id' => request()->updates_blob_id,
                    'data_blob' => $dataBlob,
                    'file_name' => $fileNames[$index] ?? null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Updates blob created successfully.',
            ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Not found exception'], 404);
        }
    }

    public function getUpdatesBlob()
    {
        $result = null;
        //// override parameters
        ///// if key is info - get all info
        if (request()->input('key') == 'info') {
            $results = UpdatesBlobs::where("updates_blob_id", request()->updates_blob_id)->get(['id', 'file_name']);
            if ($results->isNotEmpty()) {
                $ids = $results->pluck('id')->toArray();
                $fileNames = $results->pluck('file_name')->toArray();

                return response()->json([
                    'updates_blob_ids' => $ids,
                    'file_names' => $fileNames,
                ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
            } else {
                return response()->json([
                    'updates_blob_ids' => [],
                    'file_names' => [],
                ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
            }
        } else if (request()->input('key') == 'blob' && request()->input('id')) {
            ///// if key is blob - get blobs data and the id param for combination
            $result = UpdatesBlobs::where("id", request()->id)->first();
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

    public function setUpdatesBlob()
    {
        try {
            // Validate the request data
            $request = request();
            $request->validate([
                'updates_blob_id' => 'required',
                'data_blob' => 'required',
            ]);

            // Get data from the request
            $updatesBlobId = $request->updates_blob_id;
            $dataBlobs = $request->data_blob;
            $fileNames = $request->file_name;

            // Delete existing data blobs
            UpdatesBlobs::where('updates_blob_id', $updatesBlobId)->delete();

            // Prepare data for bulk insert
            foreach ($dataBlobs as $index => $dataBlob) {
                UpdatesBlobs::insert([
                    'updates_blob_id' => $updatesBlobId,
                    'data_blob' => $dataBlob,
                    'file_name' => $fileNames[$index] ?? null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Updates blob updated successfully.',
            ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Not found exception'], 404);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}

