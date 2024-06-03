<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateReportRequest;
use App\Jobs\ExportRevenueReport;
use App\Services\Admin\ExportService;

class ExportController extends Controller
{
    public function createReport(CreateReportRequest $request)
    {
        $data = $request->validated();
        ExportRevenueReport::dispatch($data);
    }
}
