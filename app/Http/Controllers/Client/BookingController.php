<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\BookingRequest;
use App\Http\Requests\Client\TemporaryBookingRequest;
use App\Services\Client\BookingService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function booking(BookingRequest $request)
    {
        $data = $request->validated();
        $result = $this->bookingService->booking($data);
        if ($result === true) {
            return $this->responseSuccess();
        }
        return $this->responseError([
            "message" => $result
        ]);
    }

    public function temporaryBooking(TemporaryBookingRequest $request)
    {
        $data = $request->validated();
        try {
            $token = $this->bookingService->temporaryBooking($data);
        } catch (Exception $e) {
            return $this->responseError([
                "message" => $e->getMessage()
            ]);
        }
        return $this->responseSuccess([
            "token" => $token
        ]);
    }

    public function getBookingsTemporary(Request $request)
    {
        $eventId = $request->get("eventId");
        $token = $request->get("token");
        if (!$token || !$eventId) $result = [];
        else $result = $this->bookingService->getListTemporaryBookings($token, $eventId);
        return $this->responseSuccess($result);
    }

    public function generateTeporaryToken()
    {
        $token = md5(request()->getClientIp() . Str::random(5));
        return $this->responseSuccess([
            "token" => $token
        ]);
    }
}
