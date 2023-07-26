<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\BibleGeneratorController;

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
Route::get('/bible-offer', function() {
    return view('bible_quote');
});

Route::post('/verify_email/{email}', [EmailVerificationController::class, 'verifyEmail'])->name('verify.email');

Route::get('/email-verification/{email}/{base}', [EmailVerificationController::class, 'verifyEmailSent']);

Route::get('/generate-bible-quote', [BibleGeneratorController::class, 'generateBibleQuote'])->name('generate.bible.quote');
// Include the API routes
require __DIR__.'/api.php';
