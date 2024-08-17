<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBookingStatusRequest;
use App\Services\Admin\BookingService;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function getBookings(int $eventId)
    {
        $result = $this->bookingService->getBookings($eventId);
        return $this->responseSuccess($result);
    }

    public function acceptBooking(UpdateBookingStatusRequest $request)
    {
        $data = $request->validated();
        $result = $this->bookingService->acceptBooking($data);
        if ($result) {
            return $this->responseSuccess();
        }
        return $this->responseError();
    }

    public function cancelBooking(int $eventId, int $clientId) {
        $result = $this->bookingService->cancelBookings($eventId, $clientId);
        if ($result) return $this->responseSuccess();
        return $this->responseError();
    }
}
