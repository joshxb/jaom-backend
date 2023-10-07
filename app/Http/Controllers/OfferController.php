<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Response\Manager\api\OfferManagerResponse;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    private $offerManagerResponse;

    public function __construct(
        OfferManagerResponse $offerManagerResponse
    ) {
        $this->offerManagerResponse = $offerManagerResponse;
    }

    public function index(Request $request)
    {
        return $this->offerManagerResponse->index($request);
    }

    public function show($id)
    {
        return $this->offerManagerResponse->show($id);
    }

    public function store(Request $request)
    {
        return $this->offerManagerResponse->store($request);
    }

    public function update(Request $request, $id)
    {
        return $this->offerManagerResponse->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->offerManagerResponse->destroy($id);
    }

    public function destroyAll()
    {
        return $this->offerManagerResponse->destroyAll();
    }

}
