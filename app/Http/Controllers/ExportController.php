<?php

namespace App\Http\Controllers;
use App\Response\Manager\api\ExportManagerResponse;

class ExportController extends Controller
{
    private $exportManagerResponse;

    public function __construct(
        ExportManagerResponse $exportManagerResponse
    ) {
        $this->exportManagerResponse = $exportManagerResponse;
    }

    public function exportOffers() {
        return $this->exportManagerResponse->exportOffers();
    }

    public function exportContacts() {
        return $this->exportManagerResponse->exportContacts();
    }

    public function exportDonations() {
        return $this->exportManagerResponse->exportDonations();
    }
}
