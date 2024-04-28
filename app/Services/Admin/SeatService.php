<?php

namespace App\Services\Admin;

use App\Exceptions\InvalidBookingException;
use App\Models\BookModel;
use App\Models\EventSeatClassModel;
use App\Models\SeatModel;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $dataInsert = [];
            foreach ($data["seats"] as $seatData) {
                if (sizeof($seatData["names"])) {
                    DB::enableQueryLog();
                    $seats = SeatModel::where("hall", $seatData["hall"])->whereIn("name", $seatData["names"])->get()->toArray();
                    foreach ($seats as $seat) {
                        $eventId = $data["event_id"];
                        $seatId = $seat["id"];
                        $ticketClassId = $data["ticket_class_id"];
                        $oldSeatsClass = EventSeatClassModel::where([
                            "event_id" => $eventId,
                            "seat_id" => $seatId
                        ])->first();
                        if ($oldSeatsClass) {
                            $oldSeatsClass->update(["ticket_class_id" => $ticketClassId]);
                            $oldSeatsClass->save();
                        } else {
                            $dataInsert[] = [
                                "event_id" => $data["event_id"],
                                "seat_id" => $seat["id"],
                                "ticket_class_id" => $data["ticket_class_id"]
                            ];
                        }
                    }
                }
            }
            EventSeatClassModel::insert($dataInsert);
        } catch (Exception $e) {
            Log::error("Set Seat Ticket Class: ", [
                "data" => $data,
                "message" => $e->getMessage()
            ]);
            DB::rollBack();
            return null;
        }
        DB::commit();
        return $this->getSeatTicketClass($data["event_id"]);
    }

    public function preBooking(array $data): bool
    {
        $seatIds = SeatModel::where(function (Builder $query) use ($data) {
            foreach ($data["seats"] as $seats) {
                $query = $query->orWhere(function (Builder $query) use ($seats) {
                    return $query->whereIn("name", data_get($seats, "names"))->where("hall",  data_get($seats, "hall"));
                });
            }
            return $query;
        })->get()->pluck("id")->toArray();
        $books = $this->getBooksWithClientNonSpecial($seatIds, $data["event_id"]);

        if (sizeof($books)) throw new InvalidBookingException($books->pluck("seat_id")->toArray());
        DB::beginTransaction();
        try {
            foreach ($seatIds as $id) {
                BookModel::updateOrCreate([
                    "event_id" => $data["event_id"],
                    "seat_id" => $id
                ], ["client_id" => $data["client_id"]]);
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
        return BookModel::where("books.event_id", $eventId)
            ->whereIn("seat_id", $seatIds)
            ->join("clients", "clients.id", "=", "books.client_id")
            ->where("clients.isSpecial", false)
            ->select(["books.*", "clients.name as client_name"])->get();
    }
}
