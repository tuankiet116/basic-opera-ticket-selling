<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\TicketService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(protected TicketService $ticketService)
    {
    }


    public function getTicketClasses(int $eventId)
    {
        $result = $this->ticketService->getTicketClasses($eventId);
        return $this->responseSuccess($result);
    }
}
