<?php

namespace App\Services\Admin;

use App\Models\TicketClassModel;

class TicketClassService
{
    public function listByEvent(int $eventId)
    {
        return TicketClassModel::where("event_id", $eventId)->get()->toArray();
    }
}
