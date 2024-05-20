<?php

namespace App\Mail;

use App\Models\ClientModel;
use App\Models\EventModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AskingPayment extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected EventModel $event, protected ClientModel $client, protected array $bookings)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Thông tin thanh toán vé “" . $this->event->name . "” ngày " . date('d-m-Y', strtotime($this->event->date)) . " tại Nhà hát Hồ Gươm 
            | Payment information for ticket(s) of the “" . $this->event->name . "” Concert on " . date('F j, Y', strtotime($this->event->date)) . " at Ho Guom Opera",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: "Mail.AskingPayment",
            with: [
                "event" => $this->event,
                "client" => $this->client,
                "bookings" => $this->bookings
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
