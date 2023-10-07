<?php

namespace App\Http\Controllers;

use App\Response\Manager\api\ImageManagerResponse;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    private $imageManagerResponse;

    public function __construct(
        ImageManagerResponse $imageManagerResponse
    ) {
        $this->imageManagerResponse = $imageManagerResponse;
    }

    public function update(Request $request)
    {
        return $this->imageManagerResponse->update($request);
    }
}

