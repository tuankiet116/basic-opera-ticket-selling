<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientModel extends Model
{
    use HasFactory;

    protected $table = "clients";
    protected $fillable = [
        "event_id",
        "name",
        "email",
        "phone_number",
        "address",
        "isSpecial"
    ];

    public function event() {
        return $this->belongsTo(EventModel::class, "event_id", "id");
    }
}
