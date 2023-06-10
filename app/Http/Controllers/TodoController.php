<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Todo;
use App\Models\User;
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

    public function checkDueDate()
    {
        $currentDate = date('Y-m-d'); // Get the current date

        // Find todos where the due_date is equal to the current date
        $todos = Todo::whereDate('due_date', $currentDate)->get();

        if ($todos->isEmpty()) {
            return response()->json([
                'message' => 'No active todos found to send notifications for the current date.',
            ]);
        }

        $result = [];

        $todos->each(function ($todo) use (&$result) {
            $notifications = Notification::where("user_id", $todo->user_id)->get();

            if ($notifications->isEmpty()) {
                return response()->json([
                    'message' => 'No notifications yet!',
                ]);
            }

            $notifications->each(function ($notification) use (&$result) {
                $jsonString = $notification->notification_object;
                $jsonString = str_replace("'", "\"", $jsonString);
                $jsonString = str_replace("\\\"", "\"", $jsonString);

                $data = json_decode($jsonString, true);

                $todoId = $data['todo_id'];
                $title = $data['title'];
                $content = $data['content'];

                if ($todoId !== null && !in_array($data, $result)) {
                    $result[] = $data;
                } elseif ($todoId === null) {
                    return response()->json([
                        'message' => 'Not a todo type notification.',
                    ]);
                }
            });
        });

        $remainingTodos = $todos->reject(function ($todo) use ($result) {
            return in_array($todo->id, array_column($result, 'todo_id'));
        });

        $remainingData = $remainingTodos->map(function ($todo) {
            return [
                'todo_id' => $todo->id,
                'title' => $todo->title,
                'content' => $todo->description,
            ];
        });

        $newNotifications = $remainingTodos->map(function ($todo) {
            $notificationData = [
                'todo_id' => $todo->id,
                'title' => $todo->title,
                'content' => $todo->description,
            ];

            $notification = new Notification();
            $notification->title = "Today marks the beginning of your todo task.";
            $notification->notification_object = json_encode([
                'todo_id' => $todo->id,
                'title' => $todo->title,
                'content' => $todo->description,
            ]);
            $notification->user_id = $todo->user_id;
            $notification->save();

            return [
                'title' => $notification->title,
                'notification_object' => $notification->notification_object,
                'user_id' => $notification->user_id,
            ];
        });

        return response()->json([
            'message' => 'Notifications sent for in due-date todos with the current date.',
            'data' => [
                'inDueDateTodo' => $remainingData,
                'newTodoNotifications' => $newNotifications,
            ],
        ]);
    }

}
