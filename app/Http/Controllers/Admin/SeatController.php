<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PreBookingRequest;
use App\Http\Requests\Admin\SetSeatTicketClassRequest;
use App\Services\Admin\SeatService;
use Exception;

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
        try {
            $result = $this->seatService->setSeatTicketClass($data);
        } catch (Exception $e) {
            return $this->responseError([
                "message" => $e->getMessage()
            ]);
        }
        return $this->responseSuccess($result);
    }

    public function preBookingTicket(PreBookingRequest $request)
    {
        $data = $request->validated();
        $result = $this->seatService->preBooking($data, $data["isCancel"]);
        if (!$result) {
            return $this->responseError([
                "message" => __("messages.errors.common")
            ]);
        }
        return $this->responseSuccess();
    }

    public function getBookingStatus(int $eventId)
    {
        $result = $this->seatService->getBookingStatus($eventId);
        return $this->responseSuccess($result->toArray());
    }
}
