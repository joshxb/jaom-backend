<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Response\Manager\api\GroupChatImageManagerResponse;
use Illuminate\Http\Request;

class GroupChatImageController extends Controller
{
    private $groupChatImageManagerResponse;

    public function __construct(
        GroupChatImageManagerResponse $groupChatImageManagerResponse
    ) {
        $this->groupChatImageManagerResponse = $groupChatImageManagerResponse;
    }

    public function getGroupImage($id)
    {
        return $this->groupChatImageManagerResponse->getGroupImage($id);
    }

    public function updateGroupImage(Request $request)
    {
        return $this->groupChatImageManagerResponse->updateGroupImage($request);
    }
}
