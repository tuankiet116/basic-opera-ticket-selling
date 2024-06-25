<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventModel;
use App\Services\Admin\TicketClassService;

class TicketClassController extends Controller
{
    public function __construct(protected TicketClassService $ticketClassService)
    {
    }

    public function list(EventModel $eventModel)
    {
        return $this->responseSuccess($this->ticketClassService->listByEvent($eventModel->id));
    }
}
