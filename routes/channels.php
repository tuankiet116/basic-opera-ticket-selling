<?php

use App\Broadcasting\AdminClientBookingTicket;
use App\Broadcasting\ClientBookingTicket;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin.client-booking-event-{eventModel}', AdminClientBookingTicket::class);
Broadcast::channel('client-booking-event-{eventModel}', ClientBookingTicket::class);
