<?php

namespace App\Http\Services;

use App\Models\EventModel;
use Carbon\Carbon;

class EventService
{
    public function listAvailableEvents()
    {
        $dateNow = Carbon::now()->toDateString();
        dd($dateNow);
        EventModel::where("date");
    }
}
