<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::orderBy('created_at', 'desc')->paginate();
        return response()->json($feedbacks);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'description' => 'required',
        ]);

        $feedback = new Feedback($validatedData);
        $feedback->user_id = $user->id;
        $feedback->save();

        return response()->json($feedback, 201);
    }

    public function show($id)
    {
        $feedback = Feedback::findOrFail($id);
        $user = Auth::user();

        if ($feedback->user_id !== $user->id) {
            return response()->json('Unauthorized', 401);
        }

        return response()->json($feedback);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'description' => 'string',
            'response_object' => 'json'
        ]);

        $feedback = Feedback::findOrFail($id);
        $user = Auth::user();

        if ($user->type == 'admin') {
        } else {
            if ($feedback->user_id !== $user->id) {
                return response()->json('Permission denied!', 401);
            }
        }

        $feedback->update($validatedData);
        return response()->json($feedback);
    }

    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $user = Auth::user();

        if ($user->type == 'admin') {
        } else {
            if ($feedback->user_id !== $user->id) {
                return response()->json('Unauthorized', 401);
            }
        }

        $feedback->delete();

        return response()->json('Feedback deleted successfully');
    }
}
