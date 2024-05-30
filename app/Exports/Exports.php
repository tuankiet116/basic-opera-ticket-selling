<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Exports
{
    public function __construct(private Spreadsheet $spreadsheet)
    {
    }
    /**
     *@param AddressRange|array<int>|CellAddress|int|string $cellCoordinate
     *A simple string containing a cell address like 'A1' or a cell range like 'A1:E10' or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]), or a CellAddress or AddressRange object.
     */
    protected function setCenterStyle($cellCoordinate)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getStyle($cellCoordinate)->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    /**
     *@param AddressRange|array<int>|CellAddress|int|string $cellCoordinate
     *A simple string containing a cell address like 'A1' or a cell range like 'A1:E10' or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]), or a CellAddress or AddressRange object.
     */
    protected function setBorderStyle($cellCoordinate) {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getStyle($cellCoordinate)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);
    }

    /**
     *@param AddressRange|array<int>|CellAddress|int|string $cellCoordinate
     *A simple string containing a cell address like 'A1' or a cell range like 'A1:E10' or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]), or a CellAddress or AddressRange object.
     */
    protected function setWrapText($coordinates) {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getStyle($coordinates)->getAlignment()->setWrapText(true);
    }
}
