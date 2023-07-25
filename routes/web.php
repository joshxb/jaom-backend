<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailVerificationController;

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

Route::get('/prayer-offer-request', function() {
    return view('prayer_offer');
});

Route::post('/verify_email/{email}', [EmailVerificationController::class, 'verifyEmail'])->name('verify.email');

Route::get('/email-verification/{email}/{base}', [EmailVerificationController::class, 'verifyEmailSent']);

// Include the API routes
require __DIR__.'/api.php';
