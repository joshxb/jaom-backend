<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\DonateTransactionResource;
use App\Models\DonateTransactions;
use App\Response\Manager\api\DonateTransactionManagerResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendDonationMail;

class DonateTransactionsController extends Controller
{
    private $donateTransactionManagerResponse;

    public function __construct(
        DonateTransactionManagerResponse $donateTransactionManagerResponse
    ) {
        $this->donateTransactionManagerResponse = $donateTransactionManagerResponse;
    }

    public function getAllIndex()
    {
        return $this->donateTransactionManagerResponse->getAllIndex();
    }

    public function index(Request $request)
    {
        return $this->donateTransactionManagerResponse->index($request);
    }

    public function show($id)
    {
        return $this->donateTransactionManagerResponse->show($id);
    }

    public function store(Request $request)
    {
        return $this->donateTransactionManagerResponse->store($request);
    }

    public function update(Request $request, $id)
    {
        return $this->donateTransactionManagerResponse->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->donateTransactionManagerResponse->destroy($id);
    }

    public function destroyAll()
    {
        return $this->donateTransactionManagerResponse->destroyAll();
    }
}
