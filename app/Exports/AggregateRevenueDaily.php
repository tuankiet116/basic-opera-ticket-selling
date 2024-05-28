<?php

namespace App\Exports;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AggregateRevenueDaily
{
    public function __construct(private Spreadsheet $spreadsheet)
    {
    }

    public function export()
    {
        $this->spreadsheet->getProperties()->setTitle("Báo cáo bán vé hàng ngày");
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:J1");
        $sheet->setCellValueExplicit([1, 1], "Báo cáo số liệu phân phối vé", DataType::TYPE_STRING);
        
        $this->saveFile();
    }

    protected function saveFile()
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->setOffice2003Compatibility(true);
        Storage::createDirectory("/reports/");
        $writer->save(storage_path("app/reports/") . time() . ".xlsx");
    }
}
