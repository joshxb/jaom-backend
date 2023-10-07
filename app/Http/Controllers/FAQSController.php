<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Response\Manager\api\FaqsManagerResponse;
use Illuminate\Http\Request;
use App\Models\faqs;

class FAQSController extends Controller
{
    private $faqsManagerResponse;

    public function __construct(
        FaqsManagerResponse $faqsManagerResponse
    ) {
        $this->faqsManagerResponse = $faqsManagerResponse;
    }

    public function index()
    {
        return $this->faqsManagerResponse->index();
    }

    public function create()
    {
        return $this->faqsManagerResponse->create();
    }

    public function store(Request $request)
    {
        return $this->faqsManagerResponse->store($request);
    }

    public function show(faqs $faq)
    {
        return $this->faqsManagerResponse->show($faq);
    }

    public function edit(faqs $faq)
    {
        return $this->faqsManagerResponse->edit($faq);
    }

    public function update(Request $request, faqs $faq)
    {
        return $this->faqsManagerResponse->update($request, $faq);
    }

    public function destroy(faqs $faq)
    {
        return $this->faqsManagerResponse->destroy($faq);
    }
}
