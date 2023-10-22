<?php

namespace App\Response\Manager\api;

use App\Http\Resources\DonateTransactionResource;
use App\Models\Contact;
use App\Models\DonateTransactions;
use App\Models\Offer;

class ExportManagerResponse
{
    public function exportOffers()
    {
        if (auth()->user()->type === 'admin' && request()->input('role') === 'admin') {
            $limit = request()->value;
            $offers = Offer::limit($limit)->get();

            return response()->json($offers);
        }
    }

    public function exportContacts()
    {
        if (auth()->user()->type === 'admin' && request()->input('role') === 'admin') {
            $limit = request()->value;
            $contacts = Contact::limit($limit)->get();

            return response()->json($contacts);
        }
    }

    public function exportDonations()
    {
        if (auth()->user()->type === 'admin' && request()->input('role') === 'admin') {
            $limit = request()->value;
            $donate = DonateTransactionResource::collection(DonateTransactions::limit($limit)->get());

            return response()->json($donate);
        }
    }
}
