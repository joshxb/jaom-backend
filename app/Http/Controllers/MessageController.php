<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Response\Manager\api\MessageManagerResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    private $messageManagerResponse;

    public function __construct(
        MessageManagerResponse $messageManagerResponse
    ) {
        $this->messageManagerResponse = $messageManagerResponse;
    }

    public function all_conversations(Request $request){
        return $this->messageManagerResponse->all_conversations($request);
    }

    public function conversations()
    {
        return $this->messageManagerResponse->conversations();
    }

    public function first_conversations()
    {
        return $this->messageManagerResponse->first_conversations();
    }

    public function messages(Conversation $conversation)
    {
        return $this->messageManagerResponse->messages($conversation);
    }

    public function send_messages(Request $request, Conversation $conversation)
    {
        return $this->messageManagerResponse->send_messages($request, $conversation);
    }

    public function clearMessages(Request $request, Conversation $conversation)
    {
        return $this->messageManagerResponse->clearMessages($request, $conversation);
    }

    public function deleteSpecificMessage(Request $request, $id)
    {
        return $this->messageManagerResponse->deleteSpecificMessage($request, $id);
    }
}
