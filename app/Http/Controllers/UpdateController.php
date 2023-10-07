<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Response\Manager\api\UpdateManagerResponse;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    private $updateManagerResponse;

    public function __construct(
        UpdateManagerResponse $updateManagerResponse
    ) {
        $this->updateManagerResponse = $updateManagerResponse;
    }

    public function updatesCounts()
    {
        return $this->updateManagerResponse->updatesCounts();
    }

    public function allUpdates()
    {
        return $this->updateManagerResponse->allUpdates();
    }

    public function index(Request $request)
    {
        return $this->updateManagerResponse->index($request);
    }

    public function store(Request $request)
    {
        return $this->updateManagerResponse->store($request);
    }

    public function show($id)
    {
        return $this->updateManagerResponse->show($id);
    }

    public function update(Request $request, $id)
    {
        return $this->updateManagerResponse->update($request, $id);
    }

    public function updatePermission($id)
    {
        return $this->updateManagerResponse->updatePermission($id);
    }

    public function destroy($id)
    {
        return $this->updateManagerResponse->destroy($id);
    }
}
