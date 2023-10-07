<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Response\Manager\api\DonationImageManagerResponse;
use Illuminate\Http\Request;

class DonationImageController extends Controller
{
    private $donationImageManagerResponse;

    public function __construct(
        DonationImageManagerResponse $donationImageManagerResponse
    ) {
        $this->donationImageManagerResponse = $donationImageManagerResponse;
    }

    public function getScreenShot($id)
    {
        return $this->donationImageManagerResponse->getScreenShot($id);
    }

    public function updateDonationSS(Request $request, $id)
    {
        return $this->donationImageManagerResponse->updateDonationSS($request, $id);
    }
}
