<?php

namespace App\Jobs;

use App\Services\Admin\ExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportRevenueReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected ExportService $exportService)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(array $data): void
    {
        $this->exportService->exportReportAggregateRevenue($data);
    }
}
