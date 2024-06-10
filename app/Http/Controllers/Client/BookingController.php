<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\BookingRequest;
use App\Http\Requests\Client\TemporaryBookingRequest;
use App\Services\Client\BookingService;
use Exception;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService)
    {
    }

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

    public function temporaryBooking(TemporaryBookingRequest $request) {
        $data = $request->validated();
        try {
            $token = $this->bookingService->temporaryBooking($data);
        } catch(Exception $e) {
            return $this->responseError([
                "message" => $e->getMessage()
            ]);
        }
        return $this->responseSuccess([
            "token" => $token
        ]);
    }
}
