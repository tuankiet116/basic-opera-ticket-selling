<?php

namespace App\Http\Services\Admin;

use App\Models\EventModel;

class EventService
{
    public function listAll(string $query = ""): array
    {
        return EventModel::searchBy($query)->paginate(PAGINATE_NUMBER);
    }
}
