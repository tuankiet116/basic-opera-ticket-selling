<?php

namespace App\Broadcasting;

use App\Models\EventModel;
use App\Models\User;

class ClientBookingTicket
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(EventModel $eventModel): array|bool
    {
        return $eventModel && $eventModel->is_openning;
    }
}
