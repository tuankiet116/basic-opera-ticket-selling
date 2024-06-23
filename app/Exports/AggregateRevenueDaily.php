<?php

namespace App\Exports;

use App\Models\EventModel;
use App\Models\EventSeatClassModel;
use App\Models\TicketClassModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AggregateRevenueDaily extends Exports
{
    public function __construct(private Spreadsheet $spreadsheet)
    {
        parent::__construct($spreadsheet);
    }

    /**
     * Get tickets distribution by each ticket class
     * @param $events Collection<EventModel>
     */
    public function getTicketDistribution(Collection $events)
    {
        $distribution = [];
        $events->each(function (EventModel $event) use (&$distribution) {
            if (!isset($distribution[$event->id])) $distribution[$event->id] = [];
            $event->ticketClasses->each(function (TicketClassModel $ticketClass) use (&$distribution, $event) {
                $count = EventSeatClassModel::where("ticket_class_id", $ticketClass->id)->count();
                $distribution[$event->id][$ticketClass->id] = $count;
            });
        });
        return $distribution;
    }

    public function getTotalClientSpecial($dataClientBookings)
    {
        return count(array_filter($dataClientBookings, fn ($client) => $client["isSpecial"]));
    }

    /**
     * @param array $dataClientBookingsOnline
     * Array of clients included events, ticket classes and seats which this client has booked for.
     * @param array $dataClientBookingsSpecial
     * Array of clients included events, ticket classes and seats which this client has booked for.
     * @param Collection<EventModel> $events
     * Collection of event models with eagle load ticket_classes
     */
    public function export(array $dataClientBookingsOnline, array $dataClientBookingsSpecial, array $eventsTicketsBooked, Collection $events, string $fileName)
    {
        $totalTickets = $this->getTicketDistribution($events);
        $totalAggregate = $this->exportSheetAggregateTicketDistribution($dataClientBookingsOnline, $totalTickets, $events, false);
        $this->spreadsheet->createSheet();
        $this->spreadsheet->setActiveSheetIndex(1);
        $dataClientBookingsSpecial = [...$dataClientBookingsSpecial, [
            "name" => "Khách hàng Online",
            "events" => $totalAggregate
        ]];
        $totalAggregate = $this->exportSheetAggregateTicketDistribution($dataClientBookingsSpecial, $totalTickets, $events, true);
        $this->exportSheetRevenue($totalAggregate, $totalTickets, $eventsTicketsBooked, $events);
        $this->saveFile($fileName);
    }

    public function exportSheetAggregateTicketDistribution(array $dataClientBookings, array $totalTickets, Collection $events, bool $isClientSpecial)
    {
        $totalTicketByEvents = [];
        $title = $isClientSpecial ? "Phân phối vé khách đặc biệt" : "Phân phối vé khách Online";
        $this->spreadsheet->getProperties()->setTitle($title);
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle($title);

        $sheet->setCellValue([1, 3], "STT");
        $sheet->mergeCells("A3:A4");

        $sheet->setCellValue([2, 3], "Đơn vị");
        $sheet->mergeCells("B3:B4");

        $sheet->setCellValue([1, 5], "Tổng vé phát hành");
        $sheet->mergeCells([1, 5, 2, 5]);

        /**
         * Render events and ticket class name
         */
        $endColumnIndex = 3;
        $events->each(function (EventModel $event) use (&$sheet, &$endColumnIndex, $totalTickets) {
            $event->ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$endColumnIndex, $totalTickets, $event) {
                $sheet->setCellValue([$endColumnIndex, 5], $totalTickets[$event->id][$ticketClass->id]);
                $endColumnIndex++;
            });
        });

        /**
         * Render tickets distribution excel
         */
        $endColumnIndex = 3;
        $events->each(function (EventModel $e) use (&$sheet, &$totalTicketByEvents, &$endColumnIndex, $events) {
            $currentColumn = $endColumnIndex;
            $ticketClasses = $e->ticketClasses;
            $eventDate = $this->formatDate($e->date);

            if (!isset($totalTicketByEvents[$e->id])) $totalTicketByEvents[$e->id] = [];
            $ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$totalTicketByEvents, &$currentColumn, $ticketClasses, $e) {
                if (!isset($totalTicketByEvents[$ticketClass->event_id][$ticketClass->id])) $totalTicketByEvents[$e->id][$ticketClass->id] = 0;
                $sheet->setCellValue([$currentColumn, 4], $ticketClass->name);
                $sheet->getColumnDimensionByColumn($currentColumn)->setAutoSize(true);
                if ($ticketClasses->last()->id == $ticketClass->id) return;
                $currentColumn++;
            });
            $sheet->setCellValue([$endColumnIndex, 3], $e->name . "($eventDate)");
            $sheet->mergeCells([$endColumnIndex, 3, $currentColumn, 3]);

            if ($events->last()->id == $e->id) $endColumnIndex = $currentColumn;
            else $endColumnIndex = $currentColumn + 1;
        });

        /**
         * Render bookings of each clients included online clients and special clients
         * @param number $endRowIndex The end row of data
         */
        $endRowIndex = 6;
        if (!sizeof($dataClientBookings)) $endRowIndex = 5;
        foreach ($dataClientBookings as $key => $client) {
            $currentColumn = 3;
            $sheet->setCellValue([1, $endRowIndex], $key + 1);
            $sheet->setCellValue([2, $endRowIndex], $client["name"]);
            $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);
            $sheet->getColumnDimensionByColumn(2)->setAutoSize(true);
            $events->each(function (EventModel $e) use (&$sheet, &$totalTicketByEvents, $endRowIndex, &$currentColumn, $client) {
                $ticketClasses = $e->ticketClasses;
                $ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$totalTicketByEvents, &$currentColumn, $client, $endRowIndex, $e) {
                    if (isset($client["events"][$e->id][$ticketClass->id])) {
                        if (is_array($client["events"][$e->id][$ticketClass->id])) $numberTicket = sizeof($client["events"][$e->id][$ticketClass->id]);
                        else $numberTicket = $client["events"][$e->id][$ticketClass->id];
                    } else $numberTicket = 0;
                    $totalTicketByEvents[$e->id][$ticketClass->id] += $numberTicket;
                    $sheet->setCellValue([$currentColumn, $endRowIndex], $numberTicket);
                    $sheet->getStyle([$currentColumn, $endRowIndex])->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                    $currentColumn++;
                });
            });
            $endRowIndex++;
        }

        /**
         * Render total tickets has been distributed to the client
         */
        $currentColumn = 3;
        $dataSize = sizeof($dataClientBookings);
        $sheet->mergeCells([1, $endRowIndex, 2, $endRowIndex]);
        $sheet->setCellValue([1, $endRowIndex], "Tổng vé đã phân phối");
        $events->each(function (EventModel $event) use (&$sheet, &$currentColumn, $endRowIndex, $dataSize) {
            $event->ticketClasses->each(function () use (&$sheet, &$currentColumn, $endRowIndex, $dataSize) {
                $columnName = Coordinate::stringFromColumnIndex($currentColumn);
                if (!$dataSize) $formula = "0";
                else $formula = $columnName . "6:$columnName" . $endRowIndex - 1;
                $sheet->setCellValueExplicit([$currentColumn, $endRowIndex], "=SUM($formula)", DataType::TYPE_FORMULA);
                $sheet->getColumnDimensionByColumn($currentColumn)->setAutoSize(true);
                $currentColumn++;
            });
        });

        $sheet->mergeCells([1, 1, $endColumnIndex, 1]);
        $this->setCenterStyle([1, 1]);
        $sheet->setCellValue([1, 1], "Báo cáo số liệu phân phối vé (" . ($isClientSpecial ? "Khách hàng đặc biệt)" : "Khách hàng Online)"));
        $sheet->getStyle([1, 1])->getFont()->setSize(12);
        $sheet->getStyle([1, 1])->getFont()->setBold(true);
        $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);

        $coordinatesTable = [1, 3, $endColumnIndex, $endRowIndex];
        $this->setBorderStyle($coordinatesTable);
        $this->setCenterStyle($coordinatesTable);
        $this->setWrapText($coordinatesTable);
        return $totalTicketByEvents;
    }

    public function exportSheetRevenue(array $totalAggregate, array $totalTickets, array $eventsTicketsBooked, Collection $events)
    {
        $currentSheetIndex = 1;
        $events->each(function ($event) use ($totalAggregate, $totalTickets, $eventsTicketsBooked, &$currentSheetIndex) {
            $this->spreadsheet->createSheet();
            $this->spreadsheet->setActiveSheetIndex(++$currentSheetIndex);
            $sheet = $this->spreadsheet->getActiveSheet();

            $title = "Doanh thu $event->name";
            $title = strlen($title) > 31 ? substr($title, 0, 27) . "..." : $title;
            $eventDate = $this->formatDate($event->date);

            $sheet->setTitle($title);
            $sheet->mergeCells("A1:E1");
            $sheet->setCellValue("A1", "Báo cáo doanh thu bán vé");
            $sheet->getStyle("A1:E1")->getFont()->setSize(12);
            $sheet->getStyle("A1:E1")->getFont()->setBold(12);
            $this->setCenterStyle("A1:E1");

            $sheet->setTitle($title);
            $sheet->mergeCells("A2:E2");
            $sheet->setCellValue("A2", "Chương trình $event->name ($eventDate)");
            $sheet->getStyle("A2:E2")->getFont()->setSize(12);
            $sheet->getStyle("A2:E2")->getFont()->setBold(12);
            $this->setCenterStyle("A2:E2");

            $sheet->setCellValue("A4", "STT");
            $sheet->setCellValue("B4", "Hạng vé");
            $sheet->setCellValue("C4", "Đơn giá (VNĐ)");
            $sheet->setCellValue("D4", "Số lượng");
            $sheet->setCellValue("E4", "Thành tiền (VNĐ)");
            $sheet->getStyle("A4:E4")->getFont()->setBold(true);
            $this->setCenterStyle("A4:E4");

            //Render table revenue
            $startIndex = 0;
            $currentRow = 5;
            $ticketClasses = $event->ticketClasses;
            $totalTicketsByEvent = isset($totalAggregate[$event->id]) ? $totalAggregate[$event->id] : 0;
            $ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$currentRow, &$startIndex, $totalTicketsByEvent) {
                $numberTickets = is_array($totalTicketsByEvent) ? $totalTicketsByEvent[$ticketClass->id] : 0;
                $sheet->setCellValue("A$currentRow", ++$startIndex);
                $sheet->setCellValue("B$currentRow", $ticketClass->name);
                $sheet->setCellValueExplicit("C$currentRow", $ticketClass->price, DataType::TYPE_NUMERIC);
                $sheet->setCellValueExplicit("D$currentRow", $numberTickets, DataType::TYPE_NUMERIC);
                $sheet->setCellValueExplicit("E$currentRow", "=C$currentRow*D$currentRow", DataType::TYPE_FORMULA);
                $sheet->getStyle("C$currentRow")->getNumberFormat()->setFormatCode("#,##0");
                $sheet->getStyle("D$currentRow")->getNumberFormat()->setFormatCode("#,##0");
                $sheet->getStyle("E$currentRow")->getNumberFormat()->setFormatCode("#,##0");
                $sheet->getColumnDimension("A")->setAutoSize(true);
                $sheet->getColumnDimension("B")->setAutoSize(true);
                $sheet->getColumnDimension("C")->setAutoSize(true);
                $sheet->getColumnDimension("D")->setAutoSize(true);
                $sheet->getColumnDimension("E")->setAutoSize(true);
                $currentRow++;
            });

            $endRowData = $currentRow - 1;
            $sheet->mergeCells("A$currentRow:C$currentRow");
            $sheet->setCellValue("A$currentRow", "Tổng cộng");
            $sheet->setCellValueExplicit("D$currentRow", "=SUM(D5:D$endRowData)", DataType::TYPE_FORMULA);
            $sheet->setCellValueExplicit("E$currentRow", "=SUM(E5:E$endRowData)", DataType::TYPE_FORMULA);
            $sheet->getStyle("A$currentRow")->getNumberFormat()->setFormatCode("#,##0");
            $sheet->getStyle("D$currentRow")->getNumberFormat()->setFormatCode("#,##0");
            $sheet->getStyle("E$currentRow")->getNumberFormat()->setFormatCode("#,##0");
            $this->setBorderStyle("A4:E$currentRow");
            $this->setCenterStyle("A4:E$currentRow");
            $sheet->getStyle("A$currentRow")->getAlignment()->setHorizontal("right");
            $sheet->getStyle("A$currentRow:E$currentRow")->getFont()->setBold(true);


            //Render table unbooked tickets
            $sheet->mergeCells("G4:J4");
            $sheet->setCellValue("G4", "Số lượng vé tồn");
            $sheet->setCellValue("G5", "Hạng vé");
            $sheet->setCellValue("H5", "Tổng vé phát hành");
            $sheet->setCellValue("I5", "Đã bán");
            $sheet->setCellValue("J5", "Số lượng tồn");
            $sheet->getColumnDimension("G")->setAutoSize(true);
            $sheet->getColumnDimension("H")->setAutoSize(true);
            $sheet->getColumnDimension("I")->setAutoSize(true);
            $sheet->getColumnDimension("J")->setAutoSize(true);
            $sheet->getStyle("G4:J5")->getFont()->setBold(true);

            $currentRow = 6;
            $ticketsBooked = data_get($eventsTicketsBooked, $event->id, []);
            $ticketsEvent = data_get($totalTickets, $event->id, []);
            $ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$currentRow, $ticketsEvent, $ticketsBooked) {
                $countSoldTickets = sizeof(data_get($ticketsBooked, $ticketClass->id, []));
                $countTotalTickets = data_get($ticketsEvent, $ticketClass->id, 0);
                $sheet->setCellValue("G$currentRow", $ticketClass->name);
                $sheet->setCellValueExplicit("H$currentRow", $countTotalTickets, DataType::TYPE_NUMERIC);
                $sheet->setCellValueExplicit("I$currentRow", $countSoldTickets, DataType::TYPE_NUMERIC);
                $sheet->setCellValueExplicit("J$currentRow", "=H$currentRow-I$currentRow", DataType::TYPE_FORMULA);
                $sheet->getStyle("H$currentRow")->getNumberFormat()->setFormatCode("#,##0");
                $sheet->getStyle("I$currentRow")->getNumberFormat()->setFormatCode("#,##0");
                $sheet->getStyle("J$currentRow")->getNumberFormat()->setFormatCode("#,##0");
                $sheet->getColumnDimension("G")->setAutoSize(true);
                $currentRow++;
            });
            $endRowData = $currentRow - 1;
            $sheet->setCellValue("G$currentRow", "Tổng cộng");
            $sheet->setCellValueExplicit("H$currentRow", "=SUM(H6:H$endRowData)", DataType::TYPE_FORMULA);
            $sheet->setCellValueExplicit("I$currentRow", "=SUM(I6:I$endRowData)", DataType::TYPE_FORMULA);
            $sheet->setCellValueExplicit("J$currentRow", "=SUM(J6:J$endRowData)", DataType::TYPE_FORMULA);
            $sheet->getStyle("H$currentRow")->getNumberFormat()->setFormatCode("#,##0");
            $sheet->getStyle("I$currentRow")->getNumberFormat()->setFormatCode("#,##0");
            $sheet->getStyle("J$currentRow")->getNumberFormat()->setFormatCode("#,##0");
            $sheet->getStyle("G$currentRow:J$currentRow")->getFont()->setBold(true);
            $this->setCenterStyle("G4:J$currentRow");
            $this->setBorderStyle("G4:J$currentRow");
        });
    }

    protected function saveFile($fileName)
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->setOffice2003Compatibility(true);
        Storage::createDirectory("/reports/");
        $writer->save(storage_path("app/reports/") . $fileName . ".xlsx");
    }

    protected function formatDate(string $date)
    {
        return Carbon::parse($date)->format("d-m-Y");
    }
}
