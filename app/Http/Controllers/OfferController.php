<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 20); // Change 'page' to 'per_page'
        $offer = Offer::where('user_id', $user->id)->paginate($perPage);

        return response()->json($offer);
    }

    public function show($id)
    {
        try {
            // Try to find the Donate model with the given ID.
            $offer = Offer::findOrFail($id);
            return response()->json($offer);
        } catch (ModelNotFoundException $exception) {
            // If the model is not found, throw an error response.
            return response()->json(['error' => 'Offer not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'fullname' => 'required|string',
            //Philippines phone number
            'phone' => 'required|string|max:20|min:11',
            'email' => 'required|email',
            'location' => 'required|string',
            'offer' => 'required'
        ]);

        // Create a new donation transaction record to the database
        Offer::create([
            "user_id" => $user->id,
            "fullname" => $request->fullname,
            "phone" => $request->phone,
            "email" => $request->email,
            "location" => $request->location,
            "offer" => $request->offer
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your offer successfully send to our ministry! 😊',
        ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
    }

    public function update(Request $request, $id)
    {
        $offer = Offer::find($id);

        // Check if the authenticated user is authorized to update the profile
        if (Auth::user()->id !== $offer->user_id) {
            return response()->json(['message' => 'Permission denied'], 401);
        }

        $requestData = $request->all();

        $offer->update($requestData);

        return response()->json([
            'data' => $offer,
            'message' => 'Data updated successfully!',
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();

        try {
            $offer = Offer::where("id", $id)->where("user_id", $user->id)->firstOrFail();

            // Check if the authenticated user has permission to delete the donation
            if ($offer->user_id !== $user->id) {
                return response()->json(['message' => 'Permission denied. You are not allowed to delete this offer list.'], 403);
            }

            $offer->delete();

            return response()->json(['message' => 'Offer deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Offer not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while processing your request'], 500);
        }
    }

    public function destroyAll()
    {
        $user = Auth::user();

        try {
            $offer = Offer::where("user_id", $user->id)->get();

            $offer->each->delete();

            return response()->json(['message' => "All Offers cleared successfully"]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Offers not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while processing your request'], 500);
        }
    }

}
