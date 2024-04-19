<?php

namespace App\Services\Admin;

use App\Models\EventSeatClassModel;
use App\Models\SeatModel;
use Exception;
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
}
