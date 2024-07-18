<?php

namespace App\Exports;

use App\Models\BookModel;
use App\Models\DiscountModel;
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
        $discounts = [];
        $totalTicketByEvents = [];
        $title = $isClientSpecial ? "Phân phối vé khách đặc biệt" : "Phân phối vé khách Online";
        $this->spreadsheet->getProperties()->setTitle($title);
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle($title);

        $sheet->setCellValue([1, 3], "STT");
        $sheet->mergeCells("A3:A4");

        $sheet->setCellValue([2, 3], "Tên đơn vị (khách hàng)");
        $sheet->mergeCells("B3:B4");

        $sheet->setCellValue([3, 3], "Địa chỉ");
        $sheet->mergeCells("C3:C4");

        $sheet->setCellValue([4, 3], "Hình thức nhận vé");
        $sheet->mergeCells("D3:D4");

        $sheet->setCellValue([5, 3], "Số điện thoại");
        $sheet->mergeCells("E3:E4");

        $sheet->setCellValue([6, 3], "Email");
        $sheet->mergeCells("F3:F4");

        $sheet->setCellValue([1, 5], "Tổng vé phát hành");
        $sheet->mergeCells([1, 5, 6, 5]);

        $sheet->setCellValue([1, 6], "Mã giảm giá");
        $sheet->mergeCells([1, 6, 6, 6]);

        $sheet->getColumnDimension("A")->setAutoSize(true);
        $sheet->getColumnDimension("B")->setAutoSize(true);
        $sheet->getColumnDimension("C")->setAutoSize(true);
        $sheet->getColumnDimension("D")->setAutoSize(true);
        $sheet->getColumnDimension("E")->setAutoSize(true);
        $sheet->getColumnDimension("F")->setAutoSize(true);

        /**
         * Render events and ticket class name and discount name
         */
        $endColumnIndex = 7;
        $events->each(function (EventModel $e) use (&$sheet, &$totalTicketByEvents, &$discounts, &$endColumnIndex, $events, $isClientSpecial) {
            $currentColumn = $endColumnIndex;
            $ticketClasses = $e->ticketClasses;
            $eventDate = $this->formatDate($e->date);
            if (!$isClientSpecial) {
                $e->discounts[] = new DiscountModel([
                    "event_id" => $e->id,
                    "ticket_class_id" => null,
                    "discount_code" => "",
                ]);
            }

            $ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$totalTicketByEvents, &$discounts, &$currentColumn, $ticketClasses, $e) {
                $discountsByTicketClass = collect($e->discounts)->filter(
                    fn (DiscountModel $discount) => $discount->ticket_class_id == $ticketClass->id || !$discount->ticket_class_id
                )->all();
                data_set($discounts, "$e->id.$ticketClass->id", $discountsByTicketClass);
                foreach ($discountsByTicketClass as $discount) {
                    data_set($totalTicketByEvents, "$e->id.$ticketClass->id.$discount->discount_code", []);
                }
                $discountColumnIndex = $currentColumn;
                foreach ($discountsByTicketClass as $discount) {
                    $sheet->setCellValue([$discountColumnIndex, 6], $discount->discount_code);
                    $discountColumnIndex++;
                }
                if (count($discounts)) $discountColumnIndex--;
                $sheet->setCellValue([$currentColumn, 4], $ticketClass->name);
                $sheet->mergeCells([$currentColumn, 4, $discountColumnIndex, 4]);
                $sheet->getColumnDimensionByColumn($currentColumn)->setAutoSize(true);
                if ($ticketClasses->last()->id == $ticketClass->id) $currentColumn = $discountColumnIndex;
                else $currentColumn = $discountColumnIndex + 1;
            });
            $sheet->setCellValue([$endColumnIndex, 3], $e->name . "($eventDate)");
            $sheet->mergeCells([$endColumnIndex, 3, $currentColumn, 3]);

            if ($events->last()->id == $e->id) $endColumnIndex = $currentColumn;
            else $endColumnIndex = $currentColumn + 1;
        });

        /**
         * Render tickets distribution excel
         */
        $rowIndex = 5;
        $endColumnIndex = 7;
        $events->each(function (EventModel $event) use (
            &$sheet,
            &$endColumnIndex,
            $totalTickets,
            $discounts,
            $rowIndex,
            $events
        ) {
            $event->ticketClasses->each(function (TicketClassModel $ticketClass) use (
                &$sheet,
                &$endColumnIndex,
                $totalTickets,
                $discounts,
                $event,
                $rowIndex,
                $events
            ) {
                $totalDiscounts = sizeof(data_get($discounts, "$event->id.$ticketClass->id", []));
                $currentColumnDiscount = $endColumnIndex + ($totalDiscounts ? $totalDiscounts - 1 : 0);
                $sheet->setCellValue([$endColumnIndex, $rowIndex], $totalTickets[$event->id][$ticketClass->id]);
                $sheet->mergeCells([$endColumnIndex, $rowIndex, $currentColumnDiscount, $rowIndex]);
                $endColumnIndex = $currentColumnDiscount;
                if ($events->last()->id == $event->id && $event->ticketClasses->last()->id == $ticketClass->id) {
                    $endColumnName = Coordinate::stringFromColumnIndex($endColumnIndex);
                    $formula = "C$rowIndex:$endColumnName$rowIndex";
                    $sheet->setCellValueExplicit([$endColumnIndex + 1, $rowIndex], "=SUM($formula)", DataType::TYPE_FORMULA);
                    return;
                };
                $endColumnIndex++;
            });
        });

        /**
         * Render bookings of each clients included online clients and special clients
         * @param number $endRowIndex The end row of data
         */
        $endRowIndex = $rowIndex + 2;
        foreach ($dataClientBookings as $key => $client) {
            $currentColumn = 7;
            $sheet->setCellValue([1, $endRowIndex], $key + 1);
            $sheet->setCellValue([2, $endRowIndex], $client["name"]);
            $sheet->setCellValue([3, $endRowIndex], $client["address"] ?? "");
            $sheet->setCellValue([4, $endRowIndex], data_get($client, "is_receive_in_opera")
                ? "Nhận vé tại nhà hát" : "Chuyển đến tận nơi");
            if (data_get($client, "is_receive_in_opera")) {
                $sheet->getStyle([4, $endRowIndex])->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('f3f57f');
            }
            $sheet->setCellValueExplicit([5, $endRowIndex], $client["phone_number"] ?? "", DataType::TYPE_STRING2);
            $sheet->setCellValue([6, $endRowIndex], $client["email"] ?? "");

            $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);
            $sheet->getColumnDimensionByColumn(2)->setAutoSize(true);
            $events->each(function (EventModel $e) use (&$sheet, &$totalTicketByEvents, $endRowIndex, &$currentColumn, $client, $events, $discounts) {
                $e->ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$totalTicketByEvents, &$currentColumn, $client, $endRowIndex, $e, $discounts) {
                    $discounts = data_get($discounts, "$e->id.$ticketClass->id");
                    foreach ($discounts as $discount) {
                        $discountCode = $discount->discount_code;
                        $bookings = data_get($client, "events.$e->id.$ticketClass->id.$discountCode", []);
                        $numberTicket = count($bookings);
                        array_push($totalTicketByEvents[$e->id][$ticketClass->id][$discountCode], ...$bookings);
                        $sheet->setCellValue([$currentColumn, $endRowIndex], $numberTicket);
                        $sheet->getStyle([$currentColumn, $endRowIndex])->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                        $currentColumn++;
                    }
                });
                if ($events->last()->id == $e->id) {
                    $endColumnName = Coordinate::stringFromColumnIndex($currentColumn - 1);
                    $formula = "C$endRowIndex:$endColumnName$endRowIndex";
                    $sheet->setCellValueExplicit([$currentColumn, $endRowIndex], "=SUM($formula)", DataType::TYPE_FORMULA);
                    $sheet->getStyle([$currentColumn, $endRowIndex])->getFont()->setSize(11);
                    $sheet->getStyle([$currentColumn, $endRowIndex])->getFont()->setBold(true);
                    $this->setCenterStyle([$currentColumn, $endRowIndex]);
                    $this->setBorderStyle([$currentColumn, $endRowIndex]);
                }
            });
            $endRowIndex++;
        }

        /**
         * Render total tickets has been distributed to the client
         */
        $currentColumn = 7;
        $dataSize = sizeof($dataClientBookings);
        $sheet->mergeCells([1, $endRowIndex, 6, $endRowIndex]);
        $sheet->setCellValue([1, $endRowIndex], "Tổng vé đã phân phối");
        $events->each(function (EventModel $event) use (&$sheet, &$currentColumn, $endRowIndex, $dataSize, $discounts, $events) {
            $event->ticketClasses->each(function (TicketClassModel $ticketClass) use (
                &$sheet,
                &$currentColumn,
                $endRowIndex,
                $dataSize,
                $discounts,
                $events,
                $event
            ) {
                $discounts = data_get($discounts, "$event->id.$ticketClass->id");
                foreach ($discounts as $discount) {
                    $columnName = Coordinate::stringFromColumnIndex($currentColumn);
                    if (!$dataSize) $formula = "0";
                    else $formula = $columnName . "7:$columnName" . $endRowIndex - 1;
                    $sheet->setCellValueExplicit([$currentColumn, $endRowIndex], "=SUM($formula)", DataType::TYPE_FORMULA);
                    $sheet->getColumnDimensionByColumn($currentColumn)->setAutoSize(true);
                    $currentColumn++;
                }
                if ($events->last()->id == $event->id) {
                    $endColumnName = Coordinate::stringFromColumnIndex($currentColumn - 1);
                    $formula = "C$endRowIndex:$endColumnName$endRowIndex";
                    $sheet->setCellValueExplicit([$currentColumn, $endRowIndex], "=SUM($formula)", DataType::TYPE_FORMULA);
                    $sheet->getStyle([$currentColumn, $endRowIndex])->getFont()->setSize(11);
                    $sheet->getStyle([$currentColumn, $endRowIndex])->getFont()->setBold(true);
                    $this->setCenterStyle([$currentColumn, $endRowIndex]);
                    $this->setBorderStyle([$currentColumn, $endRowIndex]);
                }
            });
        });

        $endColumnIndex++;
        $sheet->mergeCells([$endColumnIndex, 3, $endColumnIndex, 4]);
        $sheet->setCellValue([$endColumnIndex, 3], "Tổng cộng");

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
            $sheet->setCellValue("C4", "Mã giảm giá");
            $sheet->setCellValue("D4", "Đơn giá (VNĐ)");
            $sheet->setCellValue("E4", "Số lượng");
            $sheet->setCellValue("F4", "Thành tiền (VNĐ)");
            $sheet->getStyle("A4:F4")->getFont()->setBold(true);
            $this->setCenterStyle("A4:F4");

            //Render table revenue
            $startIndex = 0;
            $currentRow = 5;
            $ticketClasses = $event->ticketClasses;
            $totalTicketsByEvent = data_get($totalAggregate, "$event->id", []);
            $ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$currentRow, &$startIndex, $totalTicketsByEvent) {
                $startRow = $currentRow;
                $sheet->setCellValue("A$currentRow", ++$startIndex);
                $sheet->setCellValue("B$currentRow", $ticketClass->name);
                $discounts = array_keys(data_get($totalTicketsByEvent, "$ticketClass->id"));
                foreach ($discounts as $discountCode) {
                    $bookings = data_get($totalTicketsByEvent, "$ticketClass->id.$discountCode", []);
                    $bookings = array_filter($bookings, function (BookModel $booking) {
                        return !$booking->is_client_special;
                    });
                    $numberTickets = count($bookings);
                    $price = $ticketClass->price - data_get($bookings, "0.discount_price", 0);

                    $sheet->setCellValue("C$currentRow", $discountCode);
                    $sheet->setCellValueExplicit("D$currentRow", $price, DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("E$currentRow", $numberTickets, DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicit("F$currentRow", "=D$currentRow*E$currentRow", DataType::TYPE_FORMULA);
                    $sheet->getStyle("D$currentRow")->getNumberFormat()->setFormatCode("#,##0");
                    $sheet->getStyle("E$currentRow")->getNumberFormat()->setFormatCode("#,##0");
                    $sheet->getStyle("F$currentRow")->getNumberFormat()->setFormatCode("#,##0");
                    $sheet->getColumnDimension("A")->setAutoSize(true);
                    $sheet->getColumnDimension("B")->setAutoSize(true);
                    $sheet->getColumnDimension("C")->setAutoSize(true);
                    $sheet->getColumnDimension("D")->setAutoSize(true);
                    $sheet->getColumnDimension("E")->setAutoSize(true);
                    $sheet->getColumnDimension("F")->setAutoSize(true);
                    $currentRow++;
                }
                $endRow = $currentRow - 1;
                $sheet->mergeCells("A$startRow:A$endRow");
                $sheet->mergeCells("B$startRow:B$endRow");
            });

            $endRowData = $currentRow - 1;
            $sheet->mergeCells("A$currentRow:C$currentRow");
            $sheet->setCellValue("A$currentRow", "Tổng cộng");
            $sheet->setCellValueExplicit("D$currentRow", "=SUM(D5:D$endRowData)", DataType::TYPE_FORMULA);
            $sheet->setCellValueExplicit("E$currentRow", "=SUM(E5:E$endRowData)", DataType::TYPE_FORMULA);
            $sheet->setCellValueExplicit("F$currentRow", "=SUM(F5:F$endRowData)", DataType::TYPE_FORMULA);
            $sheet->getStyle("A$currentRow")->getNumberFormat()->setFormatCode("#,##0");
            $sheet->getStyle("D$currentRow")->getNumberFormat()->setFormatCode("#,##0");
            $sheet->getStyle("E$currentRow")->getNumberFormat()->setFormatCode("#,##0");
            $sheet->getStyle("F$currentRow")->getNumberFormat()->setFormatCode("#,##0");
            $this->setBorderStyle("A4:F$currentRow");
            $this->setCenterStyle("A4:F$currentRow");
            $sheet->getStyle("A$currentRow")->getAlignment()->setHorizontal("right");
            $sheet->getStyle("A$currentRow:F$currentRow")->getFont()->setBold(true);


            //Render table unbooked tickets
            $sheet->mergeCells("H4:K4");
            $sheet->setCellValue("H4", "Số lượng vé tồn");
            $sheet->setCellValue("H5", "Hạng vé");
            $sheet->setCellValue("I5", "Tổng vé phát hành");
            $sheet->setCellValue("J5", "Đã bán");
            $sheet->setCellValue("K5", "Số lượng tồn");
            $sheet->getColumnDimension("H")->setAutoSize(true);
            $sheet->getColumnDimension("I")->setAutoSize(true);
            $sheet->getColumnDimension("J")->setAutoSize(true);
            $sheet->getColumnDimension("K")->setAutoSize(true);
            $sheet->getStyle("H4:K5")->getFont()->setBold(true);

            $currentRow = 6;
            $ticketsBooked = data_get($eventsTicketsBooked, $event->id, []);
            $ticketsEvent = data_get($totalTickets, $event->id, []);
            $ticketClasses->each(function (TicketClassModel $ticketClass) use (&$sheet, &$currentRow, $ticketsEvent, $ticketsBooked) {
                $countSoldTickets = sizeof(data_get($ticketsBooked, $ticketClass->id, []));
                $countTotalTickets = data_get($ticketsEvent, $ticketClass->id, 0);
                $sheet->setCellValue("H$currentRow", $ticketClass->name);
                $sheet->setCellValueExplicit("I$currentRow", $countTotalTickets, DataType::TYPE_NUMERIC);
                $sheet->setCellValueExplicit("J$currentRow", $countSoldTickets, DataType::TYPE_NUMERIC);
                $sheet->setCellValueExplicit("K$currentRow", "=I$currentRow-J$currentRow", DataType::TYPE_FORMULA);
                $sheet->getStyle("I$currentRow")->getNumberFormat()->setFormatCode("#,##0");
                $sheet->getStyle("J$currentRow")->getNumberFormat()->setFormatCode("#,##0");
                $sheet->getStyle("K$currentRow")->getNumberFormat()->setFormatCode("#,##0");
                $sheet->getColumnDimension("H")->setAutoSize(true);
                $currentRow++;
            });
            $endRowData = $currentRow - 1;
            $sheet->setCellValue("H$currentRow", "Tổng cộng");
            $sheet->setCellValueExplicit("I$currentRow", "=SUM(I6:I$endRowData)", DataType::TYPE_FORMULA);
            $sheet->setCellValueExplicit("J$currentRow", "=SUM(J6:J$endRowData)", DataType::TYPE_FORMULA);
            $sheet->setCellValueExplicit("K$currentRow", "=SUM(K6:K$endRowData)", DataType::TYPE_FORMULA);
            $sheet->getStyle("I$currentRow")->getNumberFormat()->setFormatCode("#,##0");
            $sheet->getStyle("J$currentRow")->getNumberFormat()->setFormatCode("#,##0");
            $sheet->getStyle("K$currentRow")->getNumberFormat()->setFormatCode("#,##0");
            $sheet->getStyle("H$currentRow:K$currentRow")->getFont()->setBold(true);
            $this->setCenterStyle("H4:K$currentRow");
            $this->setBorderStyle("H4:K$currentRow");
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
