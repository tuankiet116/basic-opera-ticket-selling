<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSeatClassModel extends Model
{
    use HasFactory;

    protected $table = "event_seat_class";
    protected $fillable = [
        "event_id",
        "seat_id",
        "ticket_class_id"
    ];

    public function seat() {
        return $this->hasOne(SeatModel::class, "id", "seat_id");
    }

    public function ticketClass() {
        return $this->hasOne(TicketClassModel::class, "id", "ticket_class_id");
    }
}
