<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GroupMessage;
use App\Response\Manager\api\GroupMessageManagerResponse;
use Illuminate\Http\Request;

class GroupMessageController extends Controller
{
    private $groupMessageManagerResponse;

    public function __construct(
        GroupMessageManagerResponse $groupMessageManagerResponse
    ) {
        $this->groupMessageManagerResponse = $groupMessageManagerResponse;
    }

    public function getGroupMessagesWithUsers(Request $request, $groupId)
    {
        return $this->groupMessageManagerResponse->getGroupMessagesWithUsers($request, $groupId);
    }

    public function index()
    {
        return $this->groupMessageManagerResponse->index();
    }

    public function show(GroupMessage $groupMessage)
    {
        return $this->groupMessageManagerResponse->show($groupMessage);
    }

    public function store(Request $request)
    {
        return $this->groupMessageManagerResponse->store($request);
    }

    public function update(Request $request, GroupMessage $groupMessage)
    {
        return $this->groupMessageManagerResponse->update($request, $groupMessage);
    }

    public function destroy(Request $request, $id)
    {
        return $this->groupMessageManagerResponse->destroy($request, $id);
    }

    public function deleteGroupMessages($groupId)
    {
        return $this->groupMessageManagerResponse->deleteGroupMessages($groupId);
    }

}
