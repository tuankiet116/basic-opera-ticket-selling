<?php

namespace App\Mail;

use App\Models\BookModel;
use App\Models\ClientModel;
use App\Models\EventModel;
use App\Models\EventSeatClassModel;
use App\Models\SeatModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PaymentSuccess extends Mailable
{
    use Queueable, SerializesModels;

    protected $bookingData;
    /**
     * Create a new message instance.
     * @param Collection<BookModel> $bookings need eagle load with seats
     */
    public function __construct(protected EventModel $event, protected ClientModel $client, protected $bookings)
    {
        $bookings->each(function (BookModel $booking) use ($event) {
            $seat = $booking->seat;
            if (!isset($this->bookingData[$seat->hall])) $this->bookingData[$seat->hall] = [];
            $ticketClass = EventSeatClassModel::with("ticketClass")->where("seat_id", $seat["id"])->where("event_id", $event->id)->first();
            array_push($this->bookingData[$seat->hall], [
                "class" => $ticketClass->ticketClass->name,
                "seat" => $seat["name"],
                "price" => $ticketClass->ticketClass->price,
            ]);
        });
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thanh toán thành công|Payment Success',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: "Mail.PaymentSuccess",
            with: [
                "event" => $this->event,
                "client" => $this->client,
                "bookings" => $this->bookingData
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
