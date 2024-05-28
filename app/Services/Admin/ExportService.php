<?php

namespace App\Services\Admin;

use App\Exports\AggregateRevenueDaily;

class ExportService
{
    public function __construct(protected AggregateRevenueDaily $aggregateRevenueDaily)
    {
    }

    public function exportReportAggregateRevenue(array $data)
    {
        $this->aggregateRevenueDaily->export();
    }
}
