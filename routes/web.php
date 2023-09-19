<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\BibleGeneratorController;
use App\Http\Controllers\PasswordResetRequestController;

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

//testing for blade template
Route::get('/send-donation', function() {
    return view('send_donation');
});

Route::post('/verify_email/{email}', [EmailVerificationController::class, 'verifyEmail'])->name('verify.email');
Route::get('/email-verification/{email}/{base}', [EmailVerificationController::class, 'verifyEmailSent']);
Route::get('/generate-bible-quote', [BibleGeneratorController::class, 'generateBibleQuote'])->name('generate.bible.quote');
Route::post('/forgot-pass-request', [PasswordResetRequestController::class, 'sendRequest'])->name('forgot.pass.request');

// Include the API routes
require __DIR__.'/api.php';
    