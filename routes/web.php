<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\GroupChatController;
use App\Http\Controllers\GroupChatImageController;
use App\Http\Controllers\GroupMessageController;
use App\Http\Controllers\GroupUserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserHistoryController;
use App\Http\Controllers\UserImagesController;
use App\Http\Controllers\FAQSController;
use App\Http\Controllers\DonateTransactionsController;
use App\Http\Controllers\DonationImageController;
use App\Http\Controllers\OfferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and will be assigned
| to the "web" middleware group. Create something great!
|
*/

Route::get('api/hello', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    // Add all your API routes here
    Route::middleware(['auth:sanctum', 'throttle:1000,1'])->group(function () {
        //******************for users api**********************
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/user-range', [UserController::class, 'userRange']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::get('/search-users', [UserController::class, 'searchUsers']);
        Route::get('/search-users/{group_id}', [UserController::class, 'searchUsersWithExceptCurrentGroup']);
        Route::get('/search-users/v2/{group_id}', [UserController::class, 'searchUsersWithCurrentGroup']);

        //******************for room api**********************
        Route::get('/group-image/{id}', [GroupChatImageController::class, 'getGroupImage']);
        Route::get('/user-image', [UserImagesController::class, 'getUserImage']);
        Route::get('/other-user-image/{user_id}', [UserImagesController::class, 'getOtherUserImage']);
        Route::post('/user-image/update', [UserImagesController::class, 'updateUserImage']);
        Route::post('/group-image/update', [GroupChatImageController::class, 'updateGroupImage']);

        //******************for conversations api**********************
        Route::post('/conversations', [ConversationController::class, 'add_conversation']);
        Route::get('/conversations', [MessageController::class, 'conversations']);
        Route::get('/first-conversations', [MessageController::class, 'first_conversations']);
        Route::get('/conversations/{conversation}', [MessageController::class, 'messages']);
        Route::post('/conversations/{conversation}/message', [MessageController::class, 'send_messages']);
        Route::delete('/conversations/{conversation}/message/v1', [ConversationController::class, 'deleteConversation']);
        Route::delete('/conversations/{conversation}/message/v2', [MessageController::class, 'clearMessages']);
        Route::get('/conversations/{conversation}/other-user-id', [ConversationController::class, 'getOtherUserId']);
        Route::get('/conversations/newest/id', [ConversationController::class, 'getFirstConversationId']);

        //******************for groupchats api**********************
        Route::get('/group_chats', [GroupChatController::class, 'index']);
        Route::get('/group_chats/current_user', [GroupChatController::class, 'indexWithCurrentUser']);
        Route::get('/first-group-messages', [GroupChatController::class, 'getFirstGroupMessages']);
        Route::get('/specific-group-messages/{group_id}', [GroupChatController::class, 'getSpecificGroupMessages']);
        Route::post('/group_chats', [GroupChatController::class, 'store']);
        Route::get('/group_chats/{groupId}', [GroupChatController::class, 'show']);
        Route::put('/group_chats/{groupChat}', [GroupChatController::class, 'update']);
        Route::delete('/group_chats/{user_id}/{group_id}', [GroupChatController::class, 'destroy']);
        Route::post('/group_chats/v1/{group_id}', [GroupChatController::class, 'destroySelectedGroupUsers']);

        //******************for group-users api**********************
        Route::get('/group_users', [GroupUserController::class, 'index']);
        Route::post('/group_users', [GroupUserController::class, 'store']);
        Route::get('/group_users/{group_id}', [GroupUserController::class, 'show']);
        Route::put('/group_users/{groupUser}', [GroupUserController::class, 'update']);
        Route::delete('/group_users/{groupUser}', [GroupUserController::class, 'destroy']);

        //******************for groupchats api**********************
        Route::get('/group-chats/{groupId}/messages', [GroupMessageController::class, 'getGroupMessagesWithUsers']);
        Route::get('/group_messages', [GroupMessageController::class, 'index']);
        Route::get('/group_messages/{group_message}', [GroupMessageController::class, 'show']);
        Route::post('/group_messages', [GroupMessageController::class, 'store']);
        Route::put('/group_messages/{group_message}', [GroupMessageController::class, 'update']);
        Route::delete('/group_messages/{group_message}', [GroupMessageController::class, 'deleteGroupMessages']);

        //******************for current user updates api**********************
        Route::get('/updates/current_user', [UpdateController::class, 'index']);
        Route::post('/updates/current_user', [UpdateController::class, 'store']);
        Route::get('/updates/{id}/current_user', [UpdateController::class, 'show']);
        Route::put('/updates/{id}/current_user', [UpdateController::class, 'update']);
        Route::delete('/updates/{id}/current_user', [UpdateController::class, 'destroy']);

        //******************for todo-task api**********************
        Route::get('/todos', [TodoController::class, 'index']);
        Route::post('/todos', [TodoController::class, 'store']);
        Route::get('/todos/{id}', [TodoController::class, 'show']);
        Route::put('/todos/{id}', [TodoController::class, 'update']);
        Route::delete('/todos/{id}', [TodoController::class, 'destroy']);
        Route::get('/due_date/todos', [TodoController::class, 'checkDueDate']);

        //******************for faqs api**********************
        Route::get('/faqs', [FAQSController::class, 'index']);
        Route::post('/faqs', [FAQSController::class, 'store']);
        Route::get('/faqs/{faq}', [FAQSController::class, 'show']);
        Route::put('/faqs/{faq}', [FAQSController::class, 'update']);
        Route::delete('/faqs/{faq}', [FAQSController::class, 'destroy']);

        //******************for feedbacks api**********************
        Route::get('/feedbacks', [FeedbackController::class, 'index']);
        Route::post('/feedbacks', [FeedbackController::class, 'store']);
        Route::get('/feedbacks/{id}', [FeedbackController::class, 'show']);
        Route::put('/feedbacks/{id}', [FeedbackController::class, 'update']);
        Route::delete('/feedbacks/{id}', [FeedbackController::class, 'destroy']);

        //******************for history api**********************
        Route::get('/history', [UserHistoryController::class, 'index']);
        Route::post('/history', [UserHistoryController::class, 'store']);
        Route::get('/history/{id}', [UserHistoryController::class, 'show']);
        Route::delete('/history/{id}', [UserHistoryController::class, 'destroy']);
        Route::delete('/history', [UserHistoryController::class, 'destroyAll']);

        //******************for notifications api**********************
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/current', [NotificationController::class, 'currentIndex']);
        Route::post('/notifications', [NotificationController::class, 'store']);
        Route::get('/notifications/{id}', [NotificationController::class, 'show']);
        Route::put('/notifications/{id}', [NotificationController::class, 'update']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
        Route::delete('/notifications', [NotificationController::class, 'destroyAll']);

        //******************for transactions api**********************
        Route::get('/transactions/donate', [DonateTransactionsController::class, 'index']);
        Route::post('/transactions/donate', [DonateTransactionsController::class, 'store']);
        Route::get('/transactions/donate/{id}', [DonateTransactionsController::class, 'show']);
        Route::put('/transactions/donate/{id}', [DonateTransactionsController::class, 'update']);
        Route::delete('/transactions/donate/{id}', [DonateTransactionsController::class, 'destroy']);
        Route::delete('/transactions/donate', [DonateTransactionsController::class, 'destroyAll']);
        Route::get('/transactions/donate/ss/{id}', [DonationImageController::class, 'getScreenShot']);
        Route::post('/transactions/donate/ss/{id}', [DonationImageController::class, 'updateDonationSS']);

        //offer?per_page=2&page=1
        Route::get('/offer', [OfferController::class, 'index']);
        Route::post('/offer', [OfferController::class, 'store']);
        Route::get('/offer/{id}', [OfferController::class, 'show']);
        Route::put('/offer/{id}', [OfferController::class, 'update']);
        Route::delete('/offer/{id}', [OfferController::class, 'destroy']);
        Route::delete('/offer', [OfferController::class, 'destroyAll']);

        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
