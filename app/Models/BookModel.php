<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookModel extends Model
{
    use HasFactory;

    protected $table = "books";
    protected $fillable = [
        "event_id",
        "client_id",
        "seat_id",
        "ticket_class_id",
        "isBooked",
        "isPending",
        "start_pending",
        "is_temporary",
        "token",
        "is_client_special",
        "discount_code",
        "discount_price",
    ];

    protected $casts = [
        "isBooked" => "boolean",
        "isPending" => "boolean",
        "is_temporary" => "boolean",
        "is_client_special" => "boolean",
    ];

    public function client()
    {
        return $this->belongsTo(ClientModel::class, "client_id");
    }

    public function seat()
    {
        return $this->belongsTo(SeatModel::class, "seat_id");
    }

    public function event()
    {
        return $this->belongsTo(EventModel::class, "event_id");
    }
}
