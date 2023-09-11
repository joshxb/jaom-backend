<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ImageController extends Controller
{

    // Patch the uploaded image in database as blob
    public function update(Request $request)
    {
    }
}

