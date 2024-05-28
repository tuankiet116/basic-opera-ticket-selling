<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportRevenueTicket implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct()
    {
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
    }

    public function headings(): array
    {
        return [
            "Báo cáo bán vé"
        ];
    }
}
