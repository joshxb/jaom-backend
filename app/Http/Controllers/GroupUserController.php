<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GroupUser;
use App\Response\Manager\api\GroupUserManagerResponse;
use Illuminate\Http\Request;

class GroupUserController extends Controller
{
    private $groupUserManagerResponse;

    public function __construct(
        GroupUserManagerResponse $groupUserManagerResponse
    ) {
        $this->groupUserManagerResponse = $groupUserManagerResponse;
    }

    public function index()
    {
        return $this->groupUserManagerResponse->index();
    }

    public function store(Request $request)
    {
        return $this->groupUserManagerResponse->store($request);
    }

    public function show(Request $request)
    {
        return $this->groupUserManagerResponse->show($request);
    }

    public function update(Request $request, GroupUser $groupUser)
    {
        return $this->groupUserManagerResponse->update($request, $groupUser);
    }

    public function destroy(GroupUser $groupUser)
    {
        return $this->groupUserManagerResponse->destroy($groupUser);
    }
}
