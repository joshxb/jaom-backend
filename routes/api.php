<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\GroupChatController;
use App\Http\Controllers\GroupUserController;
use App\Http\Controllers\GroupMessageController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UpdateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('users', [UserController::class, 'index']);
    // api/users/user-range?range=xxxxx
    Route::get('users/user-range', [UserController::class, 'userRange']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    // api/search-users?search=xxxxx&&range=xxx
    Route::get('search-users', [UserController::class, 'searchUsers']);

    //update user image
    Route::put('user/image/{id}', [ImageController::class, 'update']);

    Route::post('/conversations', [ConversationController::class, 'add_conversation']);
    Route::get('/conversations', [MessageController::class, 'conversations']);
    Route::get('/conversations/{conversation}', [MessageController::class, 'messages']);
    Route::post('/conversations/{conversation}/message', [MessageController::class, 'send_messages']);
    Route::delete('/conversations/{conversation}/message/v1', [ConversationController::class, 'deleteConversation']);
    Route::delete('/conversations/{conversation}/message/v2', [MessageController::class, 'clearMessages']);
    Route::get('/conversations/{conversation}/other-user-id', [ConversationController::class, 'getOtherUserId']);
    Route::get('/conversations/newest/id', [ConversationController::class, 'getFirstConversationId']);

    Route::get('/group_chats', [GroupChatController::class, 'index']);
    Route::get('/group_chats/current_user', [GroupChatController::class, 'indexWithCurrentUser']);
    Route::get('/first-group-messages', [GroupChatController::class, 'getFirstGroupMessages']);
    Route::post('/group_chats', [GroupChatController::class, 'store']);
    Route::get('/group_chats/{groupChat}', [GroupChatController::class, 'show']);
    Route::put('/group_chats/{groupChat}', [GroupChatController::class, 'update']);
    Route::delete('/group_chats/{groupChat}', [GroupChatController::class, 'destroy']);

    Route::get('/group_users', [GroupUserController::class, 'index']);
    Route::post('/group_users', [GroupUserController::class, 'store']);
    Route::get('/group_users/{groupUser}', [GroupUserController::class, 'show']);
    Route::put('/group_users/{groupUser}', [GroupUserController::class, 'update']);
    Route::delete('/group_users/{groupUser}', [GroupUserController::class, 'destroy']);

    Route::get('/group-chats/{groupId}/messages', [GroupMessageController::class, 'getGroupMessagesWithUsers']);
    Route::get('/group_messages', [GroupMessageController::class, 'index']);
    Route::get('/group_messages/{group_message}', [GroupMessageController::class, 'show']);
    Route::post('/group_messages', [GroupMessageController::class, 'store']);
    Route::put('/group_messages/{group_message}', [GroupMessageController::class, 'update']);
    Route::delete('/group_messages/{group_message}', [GroupMessageController::class, 'destroy']);

    Route::get('/updates/current_user', [UpdateController::class, 'index']);
    Route::post('/updates/current_user', [UpdateController::class, 'store']);
    Route::get('/updates/{id}/current_user', [UpdateController::class, 'show']);
    Route::put('/updates/{id}/current_user', [UpdateController::class, 'update']);
    Route::delete('/updates/{id}/current_user', [UpdateController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
