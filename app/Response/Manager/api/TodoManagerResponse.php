<?php

namespace App\Response\Manager\api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoManagerResponse
{
    public function allTodos()
    {
        $user = Auth::user();
        if ($user->type == 'admin' && request()->has('role') && request()->input('role') == 'admin') {
            $todos = Todo::orderBy('created_at', 'DESC')->paginate(10);

            return response()->json([
                $todos,
            ]);
        } else {
            return response()->json(['message' => "You are not authorized to access this data"], 403);
        }
    }

    public function index()
    {
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
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'due_date' => 'required|date',
        ]);

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
        $todo = Todo::findOrFail($id);
        return response()->json([
            'data' => [
                'todos' => $todo,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
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
        $user = Auth::user();
        $todo = Todo::findOrFail($id);
        if ($user->id == $todo->user_id || ($user->type == 'admin' && request()->has('role') && request()->input('role') == 'admin')) {
            $todo->delete();

            return response()->json([
                'data' => [
                    'message' => "Todo deleted successfully!",
                ],
            ]);
        } else {
            return response()->json(['message' => "You are not authorized to delete this todo."], 403);
        }
    }

    public function checkDueDate()
    {
        $currentDate = date('Y-m-d');

        $todos = Todo::whereDate('due_date', $currentDate)->get();
        if ($todos->isEmpty()) {
            $result[] = [
                'message' => 'No notifications yet!',
            ];
            return;
        }

        $result = [];

        $todos->each(function ($todo) use (&$result) {
            $notifications = Notification::where("user_id", $todo->user_id)->get();

            if ($notifications->isEmpty()) {
                $result[] = [
                    'message' => 'No notifications yet!',
                ];
                return;
            }

            $notifications->each(function ($notification) use (&$result) {
                $jsonString = $notification->notification_object;

                $data = json_decode($jsonString, false);

                $todoId = $data->todo_id;
                $title = $data->title;
                $content = $data->content;

                if ($todoId !== null && !in_array($data, $result)) {
                    $result[] = $data;
                } elseif ($todoId === null) {
                    $result[] = [
                        'message' => 'Not a todo type notification.',
                    ];
                    return;
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

