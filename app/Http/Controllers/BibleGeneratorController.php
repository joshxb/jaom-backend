<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Response\Manager\web\BibleGeneratorResponse;

class BibleGeneratorController extends Controller
{
    private $bibleGeneratorResponse;

    public function __construct(
        BibleGeneratorResponse $bibleGeneratorResponse
    ) {
        $this->bibleGeneratorResponse = $bibleGeneratorResponse;
    }

    public function generateBibleQuote($retryCount = 0)
    {
        return $this->bibleGeneratorResponse->generateBibleQuote($retryCount);
    }
}
