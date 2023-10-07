<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Response\Manager\api\UserImagesManagerResponse;
use Illuminate\Http\Request;

class UserImagesController extends Controller
{
    private $userImagesManagerResponse;

    public function __construct(
        UserImagesManagerResponse $userImagesManagerResponse
    ) {
        $this->userImagesManagerResponse = $userImagesManagerResponse;
    }

    public function getUserImage()
    {
        return $this->userImagesManagerResponse->getUserImage();
    }

    public function getOtherUserImage($id)
    {
        return $this->userImagesManagerResponse->getOtherUserImage($id);
    }

    public function updateUserImage(Request $request)
    {
        return $this->userImagesManagerResponse->updateUserImage($request);
    }

    public function updateOtherImage(Request $request, $id)
    {
        return $this->userImagesManagerResponse->updateOtherImage($request, $id);
    }
}
