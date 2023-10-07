<?php

namespace App\Http\Controllers;

use App\Response\Manager\api\ContactManagerResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    private $contactManagerResponse;

    public function __construct(
        ContactManagerResponse $contactManagerResponse
    ) {
        $this->contactManagerResponse = $contactManagerResponse;
    }

    public function index()
    {
        return $this->contactManagerResponse->index();
    }

    public function show(Request $request)
    {
        return $this->contactManagerResponse->show($request);
    }

    public function store(Request $request)
    {
        return $this->contactManagerResponse->store($request);
    }

    public function destroy(Request $request)
    {
        return $this->contactManagerResponse->destroy($request);
    }
}
