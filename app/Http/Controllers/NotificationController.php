<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Response\Manager\api\NotificationManagerResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private $notificationManagerResponse;

    public function __construct(
        NotificationManagerResponse $notificationManagerResponse
    ) {
        $this->notificationManagerResponse = $notificationManagerResponse;
    }

    public function index()
    {
        return $this->notificationManagerResponse->index();
    }

    public function currentIndex()
    {
        return $this->notificationManagerResponse->currentIndex();
    }

    public function store(Request $request)
    {
        return $this->notificationManagerResponse->store($request);
    }

    public function show($id)
    {
        return $this->notificationManagerResponse->show($id);
    }

    public function update(Request $request, $id)
    {
        return $this->notificationManagerResponse->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->notificationManagerResponse->destroy($id);
    }

    public function destroyAll()
    {
        return $this->notificationManagerResponse->destroyAll();
    }
}
