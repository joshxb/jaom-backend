<?php

namespace App\Http\Controllers;

use App\Response\Manager\api\FeedbackManagerResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    private $feedbackManagerResponse;

    public function __construct(
        FeedbackManagerResponse $feedbackManagerResponse
    ) {
        $this->feedbackManagerResponse = $feedbackManagerResponse;
    }

    public function index()
    {
        return $this->feedbackManagerResponse->index();
    }

    public function store(Request $request)
    {
        return $this->feedbackManagerResponse->store($request);
    }

    public function show($id)
    {
        return $this->feedbackManagerResponse->show($id);
    }

    public function update(Request $request, $id)
    {
        return $this->feedbackManagerResponse->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->feedbackManagerResponse->destroy($id);
    }
}
