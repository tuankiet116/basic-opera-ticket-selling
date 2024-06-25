<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountModel extends Model
{
    use HasFactory;

    protected $table = "discounts";

    protected $fillable = [
        "event_id",
        "ticket_class_id",
        "discount_code",
        "discount_type",
        "note",
        "quantity",
        "quantity_used",
        "start_date",
        "end_date",
        "price_discount",
        "percentage_discount",
    ];

    public function ticketClass()
    {
        return $this->hasOne(TicketClassModel::class, "id", "ticket_class_id");
    }
}
