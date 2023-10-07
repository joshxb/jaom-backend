<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Response\Manager\api\TodoManagerResponse;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    private $todoManagerResponse;

    public function __construct(
        TodoManagerResponse $todoManagerResponse
    ) {
        $this->todoManagerResponse = $todoManagerResponse;
    }

    public function allTodos()
    {
        return $this->todoManagerResponse->allTodos();
    }

    public function index()
    {
        return $this->todoManagerResponse->index();
    }

    public function store(Request $request)
    {
        return $this->todoManagerResponse->store($request);
    }

    public function show($id)
    {
        return $this->todoManagerResponse->show($id);
    }

    public function update(Request $request, $id)
    {
        return $this->todoManagerResponse->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->todoManagerResponse->destroy($id);
    }

    public function checkDueDate()
    {
        return $this->todoManagerResponse->checkDueDate();
    }
}
