<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\EventService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class EventController extends Controller
{
    public function __construct(protected EventService $eventService)
    {
    }

    public function list()
    {
        $result = $this->eventService->listAvailableEvents();
        return $this->responseSuccess($result->toArray());
    }

    public function getEvent(int $eventId)
    {
        $event = $this->eventService->getEvent($eventId);
        $event = $event ? $event->toArray() : [];
        return $this->responseSuccess($event);
    }

    public function getBookings(int $eventId)
    {
        try {
            $bookings = $this->eventService->getBookings($eventId);
            return $this->responseSuccess($bookings);
        } catch (BadRequestException $e) {
            Log::error("Trying to get booking: " . $e->getMessage());
            return $this->responseError([
                "message" => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
