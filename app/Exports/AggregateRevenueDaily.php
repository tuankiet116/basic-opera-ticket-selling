<?php

namespace App\Exports;

use App\Models\EventModel;
use App\Models\EventSeatClassModel;
use App\Models\TicketClassModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
    public function export(array $dataClientBookingsOnline, array $dataClientBookingsSpecial, Collection $events, string $fileName)
    {
        $totalAggregate = $this->exportSheetAggregateTicketOnlineClient($dataClientBookingsOnline, $events, false);
        $this->spreadsheet->createSheet();
        $this->spreadsheet->setActiveSheetIndex(1);
        $dataClientBookingsSpecial = [...$dataClientBookingsSpecial, [
            "name" => "Khách hàng Online",
            "events" => $totalAggregate
        ]];
        $this->exportSheetAggregateTicketOnlineClient($dataClientBookingsSpecial, $events, true);
        $this->saveFile($fileName);
    }

    public function exportSheetAggregateTicketOnlineClient(array $dataClientBookings, Collection $events, bool $isClientSpecial)
    {
        $totalTicketByEvents = [];
        $title = $isClientSpecial ? "Khách hàng đặc biệt" : "Khách hàng Online";
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
        $ticketDistributed = $this->getTicketDistribution($events);
        $events->each(function (EventModel $event) use (&$sheet, &$endColumnIndex, $ticketDistributed) {
            $event->ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$endColumnIndex, $ticketDistributed, $event) {
                $sheet->setCellValue([$endColumnIndex, 5], $ticketDistributed[$event->id][$ticketClass->id]);
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
            if (!isset($totalTicketByEvents[$e->id])) $totalTicketByEvents[$e->id] = [];
            $ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$totalTicketByEvents, &$currentColumn, $ticketClasses, $e) {
                if (!isset($totalTicketByEvents[$ticketClass->event_id][$ticketClass->id])) $totalTicketByEvents[$e->id][$ticketClass->id] = 0;
                $sheet->setCellValue([$currentColumn, 4], $ticketClass->name);
                $sheet->getColumnDimensionByColumn($currentColumn)->setAutoSize(true);
                if ($ticketClasses->last()->id == $ticketClass->id) return;
                $currentColumn++;
            });
            $sheet->setCellValue([$endColumnIndex, 3], $e->name . "(" . Carbon::parse($e->date)->format("d-m-Y") . ")");
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
                    $currentColumn++;
                });
            });
            $endRowIndex++;
        }

        /**
         * Render total tickets has been distributed to the client
         */
        $currentColumn = 3;
        $ticketDistributed = $this->getTicketDistribution($events);
        $dataSize = sizeof($dataClientBookings);
        $sheet->mergeCells([1, $endRowIndex, 2, $endRowIndex]);
        $sheet->setCellValue([1, $endRowIndex], "Tổng vé đã phân phối");
        $events->each(function (EventModel $event) use (&$sheet, &$currentColumn, $endRowIndex, $dataSize) {
            $event->ticketClasses->each(function () use (&$sheet, &$currentColumn, $endRowIndex, $dataSize) {
                $columnName = Coordinate::stringFromColumnIndex($currentColumn);
                if (!$dataSize) $formula = "0";
                else $formula = $columnName . "6:$columnName" . $endRowIndex - 1;
                $sheet->setCellValue([$currentColumn, $endRowIndex], "=SUM($formula)");
                $sheet->getColumnDimensionByColumn($currentColumn)->setAutoSize(true);
                $currentColumn++;
            });
        });

        $sheet->mergeCells([1, 1, $endColumnIndex, 1]);
        $this->setCenterStyle([1, 1]);
        $sheet->setCellValue([1, 1], "Báo cáo số liệu phân phối vé (" . ($isClientSpecial ? "Khách hàng đặc biệt)" : "Khách hàng Online)"));
        $sheet->getStyle([1, 1])->getFont()->setSize(16);
        $sheet->getStyle([1, 1])->getFont()->setBold(true);
        $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);

        $coordinatesTable = [1, 3, $endColumnIndex, $endRowIndex];
        $this->setBorderStyle($coordinatesTable);
        $this->setCenterStyle($coordinatesTable);
        $this->setWrapText($coordinatesTable);
        return $totalTicketByEvents;
    }

    protected function saveFile($fileName)
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->setOffice2003Compatibility(true);
        Storage::createDirectory("/reports/");
        $writer->save(storage_path("app/reports/") . $fileName . ".xlsx");
    }
}
