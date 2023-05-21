<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{

    public function index()
    {
        // Retrieve todos associated with the current user and order by order_created column in descending order
        $todos = Todo::where('user_id', auth()->user()->id)
                     ->orderBy('created_at', 'DESC')
                     ->get();

        return response()->json([
            'data' => [
                'todos' => $todos,
            ],
        ]);
    }


    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'due_date' => 'required|date',
        ]);

        // Create a new todo
        $todo = new Todo();
        $todo->title = $request->title;
        $todo->description = $request->description;
        $todo->due_date = $request->due_date;
        $todo->user_id = auth()->user()->id;
        $todo->save();

        return response()->json([
            'todos' => $todo,
            'message' => "Todo created successfully!",
        ]);
    }

    public function show($id)
    {
        // Find a todo by its ID
        $todo = Todo::findOrFail($id);

        return response()->json([
            'data' => [
                'todos' => $todo,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        // Find a todo by its ID
        $todo = Todo::findOrFail($id);

        // Update only the provided fields
        if ($request->filled('title')) {
            $todo->title = $request->input('title');
        }
        if ($request->filled('description')) {
            $todo->description = $request->input('description');
        }
        if ($request->filled('due_date')) {
            $todo->due_date = $request->input('due_date');
        }
        $todo->save();

        return response()->json([
            'data' => [
                'todos' => $todo,
                'message' => "Todo updated successfully!",
            ],
        ]);
    }

    public function destroy($id)
    {
        // Find a todo by its ID and delete it
        $todo = Todo::findOrFail($id);
        $todo->delete();

        return response()->json([
            'data' => [
                'message' => "Todo deleted successfully!",
            ],
        ]);
    }
}
