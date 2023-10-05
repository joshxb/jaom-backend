<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DonateTransactionsController;
use App\Http\Controllers\DonationImageController;
use App\Http\Controllers\FAQSController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\GroupChatController;
use App\Http\Controllers\GroupChatImageController;
use App\Http\Controllers\GroupMessageController;
use App\Http\Controllers\GroupUserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MigrationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserHistoryController;
use App\Http\Controllers\UserImagesController;
use App\Http\Controllers\PageAnalyticsController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Resources\UserResource;
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
//  api/users/?base=l or api/users/?base=d
Route::post('/users', [UserController::class, 'store']);
Route::post('/page-analytics', [PageAnalyticsController::class, 'store']);

Route::middleware(['auth:sanctum', 'throttle:1000,1'])->group(function () {

    //******************for users api**********************
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/administrative-access', [UserController::class, 'adminAccessUsers']);
        Route::get('/count', [UserController::class, 'userCounts']);
        Route::get('/status', [UserController::class, 'countUsersByStatus']);
        Route::get('/user-range', [UserController::class, 'userRange']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    Route::prefix('search-users')->group(function () {
        // api/search-users?search=xxxxx&&range=xxx
        Route::get('/', [UserController::class, 'searchUsers']);
        // api/search-users/group_id?search=xxxxx&&range=xxx
        Route::get('/{group_id}', [UserController::class, 'searchUsersWithExceptCurrentGroup']);
        // api/search-users/v2/group_id?search=xxxxx&&range=xxx
        Route::get('/v2/{group_id}', [UserController::class, 'searchUsersWithCurrentGroup']);
    });

    //******************for room api**********************
    Route::get('/other-user-image/{user_id}', [UserImagesController::class, 'getOtherUserImage']);

    Route::prefix('user-image')->group(function () {
        Route::post('/update', [UserImagesController::class, 'updateUserImage']);
        Route::post('/{id}/update', [UserImagesController::class, 'updateOtherImage']);
        Route::get('/', [UserImagesController::class, 'getUserImage']);
    });

    Route::prefix('group-image')->group(function () {
        Route::post('/update', [GroupChatImageController::class, 'updateGroupImage']);
        Route::get('/{id}', [GroupChatImageController::class, 'getGroupImage']);
    });

    //******************for conversations api**********************
    Route::prefix('conversations')->group(function () {
        Route::post('/', [ConversationController::class, 'add_conversation']);
        Route::get('/count', [ConversationController::class, 'conversationCounts']);
        Route::get('/all', [MessageController::class, 'all_conversations']);
        Route::get('/', [MessageController::class, 'conversations']);
        Route::get('/{conversation}', [MessageController::class, 'messages']);
        Route::post('/{conversation}/message', [MessageController::class, 'send_messages']);
        Route::delete('/{conversation}/message/v1', [ConversationController::class, 'deleteConversation']);
        Route::delete('/{conversation}/message/v2', [MessageController::class, 'clearMessages']);
        Route::get('/{conversation}/other-user-id', [ConversationController::class, 'getOtherUserId']);
        Route::get('/newest/id', [ConversationController::class, 'getFirstConversationId']);
    });

    Route::put('/active/left_convo', [ConversationController::class, 'updateActiveLeftConvo']);
    Route::get('/first-conversations', [MessageController::class, 'first_conversations']);
    Route::delete('/messages/{id}', [MessageController::class, 'deleteSpecificMessage']);

    //******************for groupchats api**********************
    Route::prefix('group_chats')->group(function () {
        Route::get('/', [GroupChatController::class, 'index']);
        Route::get('/count', [GroupChatController::class, 'groupChatCounts']);
        Route::get('/current_user', [GroupChatController::class, 'indexWithCurrentUser']);
        Route::post('/', [GroupChatController::class, 'store']);
        Route::get('/{groupId}', [GroupChatController::class, 'show']);
        Route::put('/{groupChat}', [GroupChatController::class, 'update']);
        Route::put('/v2/{groupChat}', [GroupChatController::class, 'update2']);
        Route::delete('/{groupId}', [GroupChatController::class, 'destroyV2']);
        Route::delete('/{user_id}/{group_id}', [GroupChatController::class, 'destroy']);
        Route::post('/v1/{group_id}', [GroupChatController::class, 'destroySelectedGroupUsers']);
    });

    Route::put('/active/left_group_convo', [GroupChatController::class, 'updateActiveLeftGroupConvo']);
    Route::get('/first-group-messages', [GroupChatController::class, 'getFirstGroupMessages']);
    Route::get('/specific-group-messages/{group_id}', [GroupChatController::class, 'getSpecificGroupMessages']);

    //******************for group-users api**********************
    Route::prefix('group_users')->group(function () {
        Route::get('/', [GroupUserController::class, 'index']);
        Route::post('/', [GroupUserController::class, 'store']);
        Route::get('/{group_id}', [GroupUserController::class, 'show']);
        Route::put('/{groupUser}', [GroupUserController::class, 'update']);
        Route::delete('/{groupUser}', [GroupUserController::class, 'destroy']);
    });

    //******************for groupchats api**********************
    Route::get('/group-chats/{groupId}/messages', [GroupMessageController::class, 'getGroupMessagesWithUsers']);
    Route::prefix('group_messages')->group(function () {
        Route::get('/', [GroupMessageController::class, 'index']);
        Route::get('/{group_message}', [GroupMessageController::class, 'show']);
        Route::post('/', [GroupMessageController::class, 'store']);
        Route::put('/{group_message}', [GroupMessageController::class, 'update']);
        Route::delete('/{group_message}', [GroupMessageController::class, 'deleteGroupMessages']);
        Route::delete('/v2/{id}', [GroupMessageController::class, 'destroy']);
    });

    //******************for current user updates api**********************
    Route::prefix('updates')->group(function () {
        Route::get('/count', [UpdateController::class, 'updatesCounts']);
        Route::get('/', [UpdateController::class, 'allUpdates']);
        Route::get('/current_user', [UpdateController::class, 'index']);
        Route::post('/current_user', [UpdateController::class, 'store']);
        Route::get('/{id}/current_user', [UpdateController::class, 'show']);
        Route::put('/{id}/current_user', [UpdateController::class, 'update']);
        Route::put('/{id}/permission', [UpdateController::class, 'updatePermission']);
        Route::delete('/{id}/current_user', [UpdateController::class, 'destroy']);
    });

    //******************for todo-task api**********************
    Route::prefix('todos')->group(function () {
        Route::get('/', [TodoController::class, 'index']);
        Route::post('/', [TodoController::class, 'store']);
        Route::get('/{id}', [TodoController::class, 'show']);
        Route::put('/{id}', [TodoController::class, 'update']);
        Route::delete('/{id}', [TodoController::class, 'destroy']);
    });
    Route::get('/v2/todos', [TodoController::class, 'allTodos']);

    //******************for faqs api**********************
    Route::prefix('faqs')->group(function () {
        Route::get('/', [FAQSController::class, 'index']);
        Route::post('/', [FAQSController::class, 'store']);
        Route::get('/{faq}', [FAQSController::class, 'show']);
        Route::put('/{faq}', [FAQSController::class, 'update']);
        Route::delete('/{faq}', [FAQSController::class, 'destroy']);
    });

    //******************for feedbacks api**********************
    Route::prefix('feedbacks')->group(function () {
        Route::get('/', [FeedbackController::class, 'index']);
        Route::post('/', [FeedbackController::class, 'store']);
        Route::get('/{id}', [FeedbackController::class, 'show']);
        Route::put('/{id}', [FeedbackController::class, 'update']);
        Route::delete('/{id}', [FeedbackController::class, 'destroy']);
    });

    //******************for history api**********************
    Route::prefix('history')->group(function () {
        Route::get('/', [UserHistoryController::class, 'index']);
        Route::get('/all', [UserHistoryController::class, 'indexAll']);
        Route::post('/', [UserHistoryController::class, 'store']);
        Route::get('/{id}', [UserHistoryController::class, 'show']);
        Route::delete('/{id}', [UserHistoryController::class, 'destroy']);
        Route::delete('/', [UserHistoryController::class, 'destroyAll']);
    });

    //******************for notifications api**********************
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/current', [NotificationController::class, 'currentIndex']);
        Route::post('/', [NotificationController::class, 'store']);
        Route::get('/{id}', [NotificationController::class, 'show']);
        Route::put('/{id}', [NotificationController::class, 'update']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        Route::delete('/', [NotificationController::class, 'destroyAll']);
    });

    //******************for transactions api**********************
    //transactions/donate?per_page=2&page=1&month=august&year=2023
    Route::prefix('transactions/donate')->group(function () {
        Route::get('/', [DonateTransactionsController::class, 'index']);
        Route::get('/all', [DonateTransactionsController::class, 'getAllIndex']);
        Route::post('/', [DonateTransactionsController::class, 'store']);
        Route::get('/{id}', [DonateTransactionsController::class, 'show']);
        Route::put('/{id}', [DonateTransactionsController::class, 'update']);
        Route::delete('/{id}', [DonateTransactionsController::class, 'destroy']);
        Route::delete('/', [DonateTransactionsController::class, 'destroyAll']);
        Route::get('/ss/{id}', [DonationImageController::class, 'getScreenShot']);
        Route::post('/ss/{id}', [DonationImageController::class, 'updateDonationSS']);
    });

    //offer?per_page=2&page=1
    Route::prefix('offer')->group(function () {
        Route::get('/', [OfferController::class, 'index']);
        Route::post('/', [OfferController::class, 'store']);
        Route::get('/{id}', [OfferController::class, 'show']);
        Route::put('/{id}', [OfferController::class, 'update']);
        Route::delete('/{id}', [OfferController::class, 'destroy']);
        Route::delete('/', [OfferController::class, 'destroyAll']);
    });

    Route::prefix('page-analytics')->group(function () {
        Route::get('/', [PageAnalyticsController::class, 'index']);
        Route::get('/{id}', [PageAnalyticsController::class, 'show']);
        Route::delete('/{id}', [PageAnalyticsController::class, 'destroy']);
        Route::delete('/', [PageAnalyticsController::class, 'destroyAll']);
    });

    Route::prefix('configuration')->group(function () {
        Route::put('/', [ConfigurationController::class, 'update']);
    });

    Route::prefix('external-contacts')->group(function () {
        Route::get('/', [ContactController::class, 'index']);
        Route::get('/{id}', [ContactController::class, 'show']);
        Route::delete('/{id}', [ContactController::class, 'destroy']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/configuration', [ConfigurationController::class, 'show']);
Route::post('/external-contacts', [ContactController::class, 'store']);

//can update every minutes
Route::get('/due_date/todos', [TodoController::class, 'checkDueDate']);
//clear unverified email addresses
Route::get('/users/check/email_verified_at', [UserController::class, 'removeNotVerifiedEmail']);

//add migrations to database
Route::post('/migrate', [MigrationController::class, 'migrate']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user();
    return response()->json(new UserResource($user));
});
Route::get('/login-method/access/credentials', [ConfigurationController::class, 'getTrueLoginCredentials']);
