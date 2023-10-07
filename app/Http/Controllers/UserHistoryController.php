<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Response\Manager\api\UserHistoryManagerResponse;
use Illuminate\Http\Request;

class UserHistoryController extends Controller
{
    private $userHistoryManagerResponse;

    public function __construct(
        UserHistoryManagerResponse $userHistoryManagerResponse
    ) {
        $this->userHistoryManagerResponse = $userHistoryManagerResponse;
    }

    public function store(Request $request)
    {
        return $this->userHistoryManagerResponse->store($request);
    }

    public function indexAll()
    {
        return $this->userHistoryManagerResponse->indexAll();
    }

    public function index()
    {
        return $this->userHistoryManagerResponse->index();
    }

    public function show($id)
    {
        return $this->userHistoryManagerResponse->show($id);
    }

    public function destroy($id)
    {
        return $this->userHistoryManagerResponse->destroy($id);
    }

    public function destroyAll()
    {
        return $this->userHistoryManagerResponse->destroyAll();
    }
}
