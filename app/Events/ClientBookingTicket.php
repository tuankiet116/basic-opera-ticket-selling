<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\SeatModel;

class ClientBookingTicket implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     * @param array<SeatModel> $seatsBooked
     */
    public function __construct(protected array $seatsBooked, protected int $eventId)
    {
    }

    public function broadcastQueue(): string
    {
        return 'default';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('client-booking-event-' . $this->eventId)
        ];
    }

    public function broadcastWith(): array
    {
        return [
            "seats" => $this->seatsBooked,
        ];
    }
}
