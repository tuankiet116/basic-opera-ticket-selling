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

    /**
     * @param array $dataInformations
     * An array includes data follow the formula 
     *  [
     *      [
     *          "event_id" => number,
     *          "name"  => string,
     *          "ticket_classes" => array (["id" => number, "name" => ""])
     *      ]
     *  ]
     */
    public function export(array $dataInformations)
    {
        $this->spreadsheet->getProperties()->setTitle("Báo cáo bán vé hàng ngày");
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->mergeCells("A1:J1");
        $sheet->setCellValueExplicit([1, 1], "Báo cáo số liệu phân phối vé", DataType::TYPE_STRING);
        $this->setCenterStyle([1, 1]);
        $this->saveFile();
    }

    /**
     *@param AddressRange|array<int>|CellAddress|int|string $cellCoordinate
     *A simple string containing a cell address like 'A1' or a cell range like 'A1:E10' or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]), or a CellAddress or AddressRange object.
     */
    private function setCenterStyle($cellCoordinate)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getStyle($cellCoordinate)->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    protected function saveFile()
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->setOffice2003Compatibility(true);
        Storage::createDirectory("/reports/");
        $writer->save(storage_path("app/reports/") . time() . ".xlsx");
    }
}
