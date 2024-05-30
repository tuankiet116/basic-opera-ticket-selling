<?php

namespace App\Exports;

use App\Models\EventModel;
use App\Models\TicketClassModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AggregateRevenueDaily extends Exports
{
    public function __construct(private Spreadsheet $spreadsheet)
    {
        parent::__construct($spreadsheet);
    }

    /**
     * @param array $dataClientBookings
     * Array of clients included events, ticket classes and seats which this client has booked for.
     * @param Collection<EventModel> $events
     * Collection of event models with eagle load ticket_classes
     */
    public function export(array $dataClientBookings, Collection $events)
    {
        $this->spreadsheet->getProperties()->setTitle("Báo cáo bán vé hàng ngày");
        $sheet = $this->spreadsheet->getActiveSheet();

        $sheet->mergeCells("A1:J1");
        $this->setCenterStyle([1, 1]);  
        $sheet->setCellValue([1, 1], "Báo cáo số liệu phân phối vé");

        $sheet->setCellValue([1, 3], "STT");
        $sheet->mergeCells("A3:A4");

        $sheet->setCellValue([2, 3], "Đơn vị");
        $sheet->mergeCells("B3:B4");

        $sheet->setCellValue([1, 5], "Tổng vé phát hành");
        $sheet->mergeCells([1, 5, 2, 5]);


        $endColumnIndex = 3;
        $events->each(function (EventModel $e) use (&$sheet, &$endColumnIndex, $events) {
            $currentColumn = $endColumnIndex;
            $ticketClasses = $e->ticketClasses;
            $ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$currentColumn, $ticketClasses) {
                $sheet->setCellValue([$currentColumn, 4], $ticketClass->name);
                $sheet->getColumnDimensionByColumn($currentColumn)->setAutoSize(true);
                if ($ticketClasses->last()->id == $ticketClass->id) return;
                $currentColumn++;
            });
            $sheet->setCellValue([$endColumnIndex, 3], $e->name);
            $sheet->mergeCells([$endColumnIndex, 3, $currentColumn, 3]);
            if ($events->last()->id == $e->id) return;
            $endColumnIndex = $currentColumn + 1;
        });


        $coordinatesTable = [1, 3, $endColumnIndex, 4];
        $this->setBorderStyle($coordinatesTable);
        $this->setCenterStyle($coordinatesTable);
        $this->setWrapText($coordinatesTable);
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
