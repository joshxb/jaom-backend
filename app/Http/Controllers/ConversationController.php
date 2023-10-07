<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Response\Manager\api\ConversationManagerResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    private $conversationManagerResponse;

    public function __construct(
        ConversationManagerResponse $conversationManagerResponse
    ) {
        $this->conversationManagerResponse = $conversationManagerResponse;
    }

    public function conversationCounts()
    {
        return $this->conversationManagerResponse->conversationCounts();
    }

    public function add_conversation(Request $request)
    {
        return $this->conversationManagerResponse->add_conversation($request);
    }

    public function getOtherUserId(Conversation $conversation)
    {
        return $this->conversationManagerResponse->getOtherUserId($conversation);
    }

    public function getFirstConversationId()
    {
        return $this->conversationManagerResponse->getFirstConversationId();
    }

    public function deleteConversation(Request $request, Conversation $conversation)
    {
        return $this->conversationManagerResponse->deleteConversation($request, $conversation);
    }

    public function updateActiveLeftConvo(Request $request)
    {
        return $this->conversationManagerResponse->updateActiveLeftConvo($request);
    }
}
