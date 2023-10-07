<?php

namespace App\Http\Controllers;

use App\Response\Manager\api\UserManagerResponse;
use Illuminate\Http\Request;


class UserController extends Controller
{
    private $userManagerResponse;

    public function __construct(
        UserManagerResponse $userManagerResponse
    ) {
        $this->userManagerResponse = $userManagerResponse;
    }

    public function index()
    {
        return $this->userManagerResponse->index();
    }

    public function userCounts()
    {
        return $this->userManagerResponse->userCounts();
    }

    public function countUsersByStatus()
    {
        return $this->userManagerResponse->countUsersByStatus();
    }

    public function adminAccessUsers()
    {
        return $this->userManagerResponse->adminAccessUsers();
    }

    public function userRange(Request $request)
    {
        return $this->userManagerResponse->userRange($request);
    }

    public function store(Request $request)
    {
        return $this->userManagerResponse->store($request);
    }

    public function show(string $id)
    {
        return $this->userManagerResponse->show($id);
    }

    public function update(Request $request, string $id)
    {
        return $this->userManagerResponse->update($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        return $this->userManagerResponse->destroy($request, $id);
    }

    public function searchUsers(Request $request)
    {
        return $this->userManagerResponse->searchUsers($request);
    }

    public function searchUsersWithExceptCurrentGroup(Request $request)
    {
        return $this->userManagerResponse->searchUsersWithExceptCurrentGroup($request);
    }

    public function searchUsersWithCurrentGroup(Request $request)
    {
        return $this->userManagerResponse->searchUsersWithCurrentGroup($request);
    }

    public function removeNotVerifiedEmail()
    {
        return $this->userManagerResponse->removeNotVerifiedEmail();
    }
}
