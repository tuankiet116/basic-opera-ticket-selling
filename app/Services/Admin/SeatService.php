<?php

namespace App\Services\Admin;

use App\Events\ClientBookingTicket;
use App\Events\ClientRemoveBookingTicket;
use App\Exceptions\InvalidBookingException;
use App\Models\BookModel;
use App\Models\EventModel;
use App\Models\EventSeatClassModel;
use App\Models\SeatModel;
use App\Models\TicketClassModel;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Ticket;

class SeatService
{
    public function getSeatTicketClass(int $eventId)
    {
        return EventSeatClassModel::with(["seat", "ticketClass"])->where([
            "event_id" => $eventId
        ])->get()->toArray();
    }

    public function setSeatTicketClass(array $data)
    {
        DB::beginTransaction();
        try {
            $event = EventModel::find($data["event_id"]);
            if ($event->is_openning) throw new Exception("Sự kiện đang mở bán vé, không thể cập nhật.");
            $ticketClassId = $data["ticket_class_id"];
            $ticketClass = TicketClassModel::findOrFail($ticketClassId);

            foreach ($data["seats"] as $seatData) {
                if (sizeof($seatData["names"])) {
                    $seats = SeatModel::where("hall", $seatData["hall"])->whereIn("name", $seatData["names"])->get()->toArray();
                    $seatIds = collect($seats)->pluck("id")->toArray();
                    $seatBookedCount = BookModel::where([
                        "event_id" => $event->id,
                        "is_client_special" => false
                    ])->whereIn("seat_id", $seatIds)->count();
                    if ($seatBookedCount) throw new Exception("Không thể cập nhật hạng vé cho ghế đã được đặt chỗ.");
                    foreach ($seats as $seat) {
                        $eventId = $data["event_id"];
                        $seatId = $seat["id"];
                        $oldSeatsClass = EventSeatClassModel::where([
                            "event_id" => $eventId,
                            "seat_id" => $seatId
                        ])->first();
                        $seatBookedSpecial = BookModel::where([
                            "event_id" => $event->id,
                            "is_client_special" => true,
                            "seat_id" => $seatId
                        ])->first();

                        if ($seatBookedSpecial) {
                            $seatBookedSpecial->update([
                                "ticket_class_id" => $ticketClassId,
                                "pricing" => $ticketClass->price
                            ]);
                            $seatBookedSpecial->save();
                        }

                        if ($oldSeatsClass) {
                            $oldSeatsClass->update(["ticket_class_id" => $ticketClassId]);
                            $oldSeatsClass->save();
                            continue;
                        }

                        $dataCreate = [
                            "event_id" => $data["event_id"],
                            "seat_id" => $seat["id"],
                            "ticket_class_id" => $data["ticket_class_id"]
                        ];
                        EventSeatClassModel::create($dataCreate);
                    }
                }
            }
        } catch (Exception $e) {
            Log::error("Set Seat Ticket Class: ", [
                "data" => $data,
                "message" => $e->getMessage()
            ]);
            DB::rollBack();
            throw $e;
        }
        DB::commit();
        return $this->getSeatTicketClass($data["event_id"]);
    }

    public function preBooking(array $data, bool $isCancel = false): bool
    {
        $event = EventModel::find($data["event_id"]);
        if (!$event || $event->is_delete) return false;
        $seats = SeatModel::where(function (Builder $query) use ($data) {
            foreach ($data["seats"] as $seats) {
                $query = $query->orWhere(function (Builder $query) use ($seats) {
                    return $query->whereIn("name", data_get($seats, "names"))->where("hall",  data_get($seats, "hall"));
                });
            }
            return $query;
        })->get();
        $seatIds = $seats->pluck("id")->toArray();
        $bookingsOnline = $this->getBooksWithClientNonSpecial($seatIds, $data["event_id"]);
        if (sizeof($bookingsOnline)) throw new InvalidBookingException($bookingsOnline->pluck("seat_id")->toArray());

        $eventTicketClasses = EventSeatClassModel::where("event_id", $event->id)->whereIn("seat_id", $seatIds)->get();
        $ticketClassIds = collect($eventTicketClasses)->pluck("ticket_class_id");
        if (sizeof($ticketClassIds) != sizeof($seatIds)) throw new Exception("Không thể prebooking khi chưa có hạng vé");
        $ticketClasses = TicketClassModel::whereIn("id", $ticketClassIds)->get();
        DB::beginTransaction();
        try {
            foreach ($seatIds as $id) {
                $ticketClassId = collect($eventTicketClasses)->where("seat_id", $id)->first()->ticket_class_id;
                $ticketClass = collect($ticketClasses)->where("id", $ticketClassId)->first();
                if ($isCancel) {
                    BookModel::where([
                        "event_id" => $data["event_id"],
                        "seat_id" => $id
                    ])->delete();
                } else {
                    BookModel::updateOrCreate([
                        "event_id" => $data["event_id"],
                        "seat_id" => $id,
                        "is_client_special" => true,
                        "ticket_class_id" => $ticketClass->id,
                        "pricing" => $ticketClass->price,
                    ], ["client_id" => $data["client_id"]]);
                }
            }

            $seats = array_chunk($seats->all(), CHUNK_SIZE_BROADCAST);
            foreach ($seats as $seatsChunk) {
                if ($isCancel) ClientRemoveBookingTicket::dispatch($seatsChunk, $event);
                else ClientBookingTicket::dispatch($seatsChunk, $event->id);
            }
        } catch (Exception $e) {
            Log::error("Pre Booking: ", [
                "data" => $data,
                "message" => $e->getMessage()
            ]);
            DB::rollBack();
            return false;
        }
        DB::commit();
        return true;
    }

    public function getBookingStatus($eventId)
    {
        return BookModel::with(["client", "seat"])->where("event_id", $eventId)->get();
    }

    private function getBooksWithClientNonSpecial(array $seatIds, int $eventId): ?Collection
    {
        return BookModel::where("event_id", $eventId)
            ->where("is_client_special", false)
            ->whereIn("seat_id", $seatIds)->get();
    }
}
