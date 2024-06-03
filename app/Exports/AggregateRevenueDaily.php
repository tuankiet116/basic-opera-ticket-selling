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
     * @param array $dataClientBookings
     * Array of clients included events, ticket classes and seats which this client has booked for.
     * @param Collection<EventModel> $events
     * Collection of event models with eagle load ticket_classes
     */
    public function export(array $dataClientBookings, Collection $events, string $fileName)
    {
        $this->spreadsheet->getProperties()->setTitle("Báo cáo bán vé hàng ngày");
        $sheet = $this->spreadsheet->getActiveSheet();

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
        $events->each(function (EventModel $e) use (&$sheet, &$endColumnIndex, $events) {
            $currentColumn = $endColumnIndex;
            $ticketClasses = $e->ticketClasses;
            $ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$currentColumn, $ticketClasses) {
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
            if (
                !isset($dataClientBookings[$key - 1]) || ($client["isSpecial"] != $dataClientBookings[$key - 1]["isSpecial"])
            ) {
                $text = $client["isSpecial"] ? "Khách hàng đặc biệt" : "Khách mua Online";
                $sheet->mergeCells([1, $endRowIndex, $endColumnIndex, $endRowIndex]);
                $sheet->setCellValue([1, $endRowIndex], $text);
                $endRowIndex++;
            }
            $sheet->setCellValue([1, $endRowIndex], $key + 1);
            $sheet->setCellValue([2, $endRowIndex], $client["name"]);
            $events->each(function (EventModel $e) use (&$sheet, $endRowIndex, &$currentColumn, $client) {
                $ticketClasses = $e->ticketClasses;
                $ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$currentColumn, $client, $endRowIndex, $e) {
                    $numberTicket = isset($client["events"][$e->id][$ticketClass->id]) ? sizeof($client["events"][$e->id][$ticketClass->id]) : 0;
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
        $totalClientSpecial = $this->getTotalClientSpecial($dataClientBookings);
        $totalClientNoneSpecial = sizeof($dataClientBookings) - $totalClientSpecial;
        $sheet->mergeCells([1, $endRowIndex, 2, $endRowIndex]);
        $sheet->setCellValue([1, $endRowIndex], "Tổng vé đã phân phối");
        $events->each(function (EventModel $event) use (&$sheet, &$currentColumn, $endRowIndex, $totalClientSpecial, $totalClientNoneSpecial) {
            $event->ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$currentColumn, $endRowIndex, $totalClientSpecial, $totalClientNoneSpecial) {
                $formula = "";
                $columnName = Coordinate::stringFromColumnIndex($currentColumn);
                if (!$totalClientSpecial && !$totalClientNoneSpecial) {
                    $formula = "0";
                } else {
                    if ($totalClientSpecial) {
                        $formula .= $columnName . "7:" . $columnName . (7 + $totalClientSpecial - 1);
                    }

                    if ($totalClientNoneSpecial) {
                        $start = 6 + $totalClientSpecial + 1;
                        $end = 6 + $totalClientSpecial + $totalClientNoneSpecial;
                        if ($totalClientSpecial) {
                            $start++;
                            $end++;
                            $formula .= ",";
                        }
                        $formula .= "$columnName$start:$columnName$end";
                    }
                }
                $sheet->setCellValue([$currentColumn, $endRowIndex], "=SUM($formula)");
                $currentColumn++;
            });
        });

        $sheet->mergeCells([1, 1, $endColumnIndex, 1]);
        $this->setCenterStyle([1, 1]);
        $sheet->setCellValue([1, 1], "Báo cáo số liệu phân phối vé");
        $sheet->getStyle([1, 1])->getFont()->setSize(16);
        $sheet->getStyle([1, 1])->getFont()->setBold(true);

        $coordinatesTable = [1, 3, $endColumnIndex, $endRowIndex];
        $this->setBorderStyle($coordinatesTable);
        $this->setCenterStyle($coordinatesTable);
        $this->setWrapText($coordinatesTable);
        $this->saveFile($fileName);
    }

    public function exportSheetAggregateTicketOnlineClient()
    {
        
    }

    protected function saveFile($fileName)
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->setOffice2003Compatibility(true);
        Storage::createDirectory("/reports/");
        $writer->save(storage_path("app/reports/") . $fileName . ".xlsx");
    }
}
