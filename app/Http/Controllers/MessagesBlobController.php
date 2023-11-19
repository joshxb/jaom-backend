<?php

namespace App\Http\Controllers;
use App\Response\Manager\api\MessagesBlobManagerResponse;

class MessagesBlobController extends Controller
{
    private $messagesBlobManagerResponse;

    public function __construct(
        MessagesBlobManagerResponse $messagesBlobManagerResponse
    ) {
        $this->messagesBlobManagerResponse = $messagesBlobManagerResponse;
    }


    public function getMessagesBlob(){
        return $this->messagesBlobManagerResponse->getMessagesBlob();
    }

    public function store() {
        return $this->messagesBlobManagerResponse->store();
    }
}
