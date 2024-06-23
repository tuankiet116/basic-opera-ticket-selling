<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketClassModel extends Model
{
    use HasFactory;

    protected $table = "ticket_classes";
    protected $fillable = [
        "name",
        "price",
        "color",
        "event_id"
    ];
}
