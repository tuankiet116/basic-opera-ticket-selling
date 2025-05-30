<?php

namespace App\Events;

use App\Models\EventModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\SeatModel;

class ClientRemoveBookingTicket implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     * @param array<SeatModel> $seatsRemoved
     */
    public function __construct(protected array $seatsRemoved, protected EventModel $eventModel)
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
            new Channel('client-booking-event-' . $this->eventModel->id)
        ];
    }

    public function broadcastWith(): array
    {
        return [
            "seats" => $this->seatsRemoved,
        ];
    }
}
