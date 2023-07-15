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

Route::get('jaom-server', function () {
    return view('welcome');
});

// Include the API routes
require __DIR__.'/api.php';
