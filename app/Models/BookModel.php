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
        "isBooked",
        "isPending",
        "start_pending",
    ];

    protected $casts = [
        "isBooked" => "boolean",
        "isPending" => "boolean"
    ];

    public function client()
    {
        return $this->belongsTo(ClientModel::class, "client_id");
    }

    public function seat()
    {
        return $this->belongsTo(SeatModel::class, "seat_id");
    }
}
