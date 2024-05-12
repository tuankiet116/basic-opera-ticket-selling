<?php

namespace App\Services\Client;

use App\Models\EventSeatClassModel;
use App\Models\TicketClassModel;
use App\Services\Admin\SeatService;

class TicketService
{
    public function __construct(protected SeatService $seatService)
    {
    }

    public function getTicketClasses(int $eventId): array
    {
        $seatTicketClasses = $this->seatService->getSeatTicketClass($eventId);
        $ticketClasses = TicketClassModel::where("event_id", $eventId)->get()->toArray();
        return [
            "seatTicketClasses" => $seatTicketClasses,
            "ticketClasses" => $ticketClasses
        ];
    }
}
