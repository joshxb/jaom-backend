<?php

namespace App\Http\Controllers;

use App\Response\Manager\api\PageAnalyticsManagerResponse;

class PageAnalyticsController extends Controller
{
    private $pageAnalyticsManagerResponse;

    public function __construct(
        PageAnalyticsManagerResponse $pageAnalyticsManagerResponse
    ) {
        $this->pageAnalyticsManagerResponse = $pageAnalyticsManagerResponse;
    }

    public function index()
    {
        return $this->pageAnalyticsManagerResponse->index();
    }

    public function show($id)
    {
        return $this->pageAnalyticsManagerResponse->show($id);
    }

    public function store()
    {
        return $this->pageAnalyticsManagerResponse->store();
    }

    public function destroy($id)
    {
        return $this->pageAnalyticsManagerResponse->destroy($id);
    }

    public function destroyAll()
    {
        return $this->pageAnalyticsManagerResponse->destroyAll();
    }
}
