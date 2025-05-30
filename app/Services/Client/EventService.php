<?php

namespace App\Services\Client;

use App\Models\BookModel;
use App\Models\EventModel;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class EventService
{
    public function listAvailableEvents()
    {
        return EventModel::available()->orderBy("created_at", "desc")->paginate(PAGINATE_NUMBER);
    }

    public function getEvent(int $eventId)
    {
        return EventModel::available()->find($eventId);
    }

    public function getBookings(int $eventId)
    {
        $event = $this->getEvent($eventId);
        if (!$event) throw new BadRequestException("Event not found or not available for event Id $eventId");
        return BookModel::with(["seat"])->where("event_id", $eventId)
            ->get(["event_id", "seat_id"])->toArray();
    }
}
