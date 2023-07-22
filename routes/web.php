<?php
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email-verification', function () {
    return view('email_verification');
});

// Include the API routes
require __DIR__.'/api.php';
