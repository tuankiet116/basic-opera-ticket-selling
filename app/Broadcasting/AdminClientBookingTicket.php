<?php

namespace App\Broadcasting;

use App\Models\EventModel;
use App\Models\User;

class AdminClientBookingTicket
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
    public function join(User $user, EventModel $eventModel): array|bool
    {
        return $user && $eventModel;
    }
}
