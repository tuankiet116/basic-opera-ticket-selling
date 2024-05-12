<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\BookingRequest;
use App\Services\Client\BookingService;
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
}
