<?php

namespace App\Http\Controllers;

use App\Response\Manager\api\GroupMessagesBlobManagerResponse;
use Illuminate\Http\Request;

class GroupMessagesBlobController extends Controller
{
    private $groupMessagesBlobManagerResponse;

    public function __construct(
        GroupMessagesBlobManagerResponse $groupMessagesBlobManagerResponse
    ) {
        $this->groupMessagesBlobManagerResponse = $groupMessagesBlobManagerResponse;
    }

    public function getGroupMessagesBlob(){
        return $this->groupMessagesBlobManagerResponse->getGroupMessagesBlob();
    }

    public function store() {
        return $this->groupMessagesBlobManagerResponse->store();
    }
}
