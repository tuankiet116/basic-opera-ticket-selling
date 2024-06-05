<?php

use App\Broadcasting\AdminClientBookingTicket;
use App\Broadcasting\AdminNotify;
use App\Broadcasting\ClientBookingTicket;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin.client-booking-event-{eventModel}', AdminClientBookingTicket::class);
Broadcast::channel('admin.notifications', AdminNotify::class);
Broadcast::channel('client-booking-event-{eventModel}', ClientBookingTicket::class);
