<?php

namespace App\Services\Admin;

use App\Exports\AggregateRevenueDaily;
use App\Models\BookModel;
use Illuminate\Support\Facades\DB;

class ExportService
{
    public function __construct(protected AggregateRevenueDaily $aggregateRevenueDaily)
    {
    }

    public function exportReportAggregateRevenue(array $data)
    {
        $startDate = data_get($data, "start_date");
        $endDate = data_get($data, "end_date");
        $bookings = BookModel::with(["client", "seat", "event"])
            ->whereDate("created_at", ">=", $startDate)
            ->whereDate("created_at", "<=", $endDate)
            ->where("isBooked", true)
            ->whereIn("event_id", data_get($data, "events"))->get();
        $this->aggregateRevenueDaily->export($data);
    }
}
