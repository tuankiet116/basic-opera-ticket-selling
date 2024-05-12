<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        "isSpecial",
        "is_receive_in_opera",
    ];

    protected $casts = [
        "isSpecial" => "boolean",
        "is_receive_in_opera" => "boolean",
    ];

    public function scopeSearch(Builder $query, $search)
    {
        $search = $search ? strtolower($search) : "";
        return $query->whereRaw("LOWER(clients.name) LIKE ?", ["%$search%"])
            ->orwhereRaw("LOWER(clients.email) LIKE ?", ["%$search%"])
            ->orwhereRaw("LOWER(clients.phone_number) LIKE ?", ["%$search%"])
            ->join("events", "events.id", "=", "clients.event_id", "left")
            ->whereRaw("LOWER(events.name) LIKE ?", ["%$search%"])
            ->selectRaw("clients.*, events.name as event_name, events.id as event_id");
    }

    public function event()
    {
        return $this->belongsTo(EventModel::class, "event_id", "id");
    }
}
