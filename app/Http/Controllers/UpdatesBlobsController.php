<?php

namespace App\Http\Controllers;

use App\Response\Manager\api\UpdatesBlobManagerResponse;
use Illuminate\Http\Request;

class UpdatesBlobsController extends Controller
{
    private $updatesBlobManagerResponse;

    public function __construct(
        UpdatesBlobManagerResponse $updatesBlobManagerResponse
    ) {
        $this->updatesBlobManagerResponse = $updatesBlobManagerResponse;
    }

    public function store() {
        return $this->updatesBlobManagerResponse->store();
    }

    public function getUpdatesBlob(){
        return $this->updatesBlobManagerResponse->getUpdatesBlob();
    }

    public function setUpdatesBlob(){
        return $this->updatesBlobManagerResponse->setUpdatesBlob();
    }
}
