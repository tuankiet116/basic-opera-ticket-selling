<?php

namespace App\Exceptions;

use App\Models\SeatModel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvalidBookingException extends Exception
{
    protected $seatsName = [];
    public function __construct(protected array $seatsInvalid = [])
    {
        $this->seatsName = SeatModel::whereIn("id", $seatsInvalid)->get()->pluck("name")->toArray();
    }

    public function render(Request $request): JsonResponse
    {
        if ($request->route()->getName() == "pre-booking") {
            return response()->json(data: [
                "message" => __("messages.seat_invalid.is_booked_by_online", ["value" => implode(",", $this->seatsName)]),
            ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json(data: [
            "message" => __("messages.seat_invalid.is_booked", ["value" => implode(",", $this->seatsName)]),
        ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
