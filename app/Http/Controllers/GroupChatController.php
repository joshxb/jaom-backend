<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GroupChat;
use App\Response\Manager\api\GroupChatManagerResponse;
use Illuminate\Http\Request;

class GroupChatController extends Controller
{
    private $groupChatManagerResponse;

    public function __construct(
        GroupChatManagerResponse $groupChatManagerResponse
    ) {
        $this->groupChatManagerResponse = $groupChatManagerResponse;
    }

    public function groupChatCounts()
    {
        return $this->groupChatManagerResponse->groupChatCounts();
    }

    public function indexWithCurrentUser()
    {
        return $this->groupChatManagerResponse->indexWithCurrentUser();
    }

    public function getFirstGroupMessages(Request $request)
    {
        return $this->groupChatManagerResponse->getFirstGroupMessages($request);
    }

    public function getSpecificGroupMessages(Request $request)
    {
        return $this->groupChatManagerResponse->getSpecificGroupMessages($request);
    }

    public function index(Request $request)
    {
        return $this->groupChatManagerResponse->index($request);
    }

    public function store(Request $request)
    {
        return $this->groupChatManagerResponse->store($request);
    }

    public function show(Request $request)
    {
        return $this->groupChatManagerResponse->show($request);
    }

    public function update(Request $request, GroupChat $groupChat)
    {
        return $this->groupChatManagerResponse->update($request, $groupChat);
    }

    public function update2(Request $request, $groupChat)
    {
        return $this->groupChatManagerResponse->update2($request, $groupChat);
    }

    public function destroy($user_id, $group_id)
    {
        return $this->groupChatManagerResponse->destroy($user_id, $group_id);
    }

    public function destroyV2($group_id)
    {
        return $this->groupChatManagerResponse->destroyV2($group_id);
    }

    public function destroySelectedGroupUsers(Request $request)
    {
        return $this->groupChatManagerResponse->destroySelectedGroupUsers($request);
    }

    public function updateActiveLeftGroupConvo(Request $request)
    {
        return $this->groupChatManagerResponse->updateActiveLeftGroupConvo($request);
    }
}
