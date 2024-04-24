<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PreBookingRequest;
use App\Http\Requests\Admin\SetSeatTicketClassRequest;
use App\Services\Admin\SeatService;

class SeatController extends Controller
{
    public function __construct(protected SeatService $seatService)
    {
    }

    public function getSeatTicketClass(int $eventId)
    {
        $result = $this->seatService->getSeatTicketClass($eventId);
        return $this->responseSuccess($result);
    }

    public function setSeatTicketClass(SetSeatTicketClassRequest $request)
    {
        $data = $request->validated();
        $result = $this->seatService->setSeatTicketClass($data);
        if ($result) {
            return $this->responseSuccess($result);
        }
        return $this->responseError([
            "message" => __("messages.errors.common")
        ]);
    }

    public function preBookingTicket(PreBookingRequest $request)
    {
        $data = $request->validated();
        dd($data);
    }
}
