<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateReportRequest;
use App\Services\Admin\ExportService;

class ExportController extends Controller
{
    public function __construct(protected ExportService $exportService)
    {
    }

    public function exportReport()
    {
        
    }

    public function createReport(CreateReportRequest $request)
    {
        $data = $request->validated();
        $this->exportService->exportReportAggregateRevenue($data);
    }
}
