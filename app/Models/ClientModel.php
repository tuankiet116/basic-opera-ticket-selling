<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

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

    protected $casts = [
        "isSpecial" => "boolean"
    ];

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where("LOWER(name)", "LIKE", "%" . strtolower($search) . "%")
            ->orWhere("LOWER(email)", "LIKE", "%" . strtolower($search) . "%")
            ->orWhere("LOWER(phone_number)", "LIKE", "%" . strtolower($search) . "%")
            ->join("events", "events.id", "=", "clients.event_id")
            ->where("LOWER(events.name)", "LIKE", "%" . strtolower($search) . "%");
    }

    public function event()
    {
        return $this->belongsTo(EventModel::class, "event_id", "id");
    }
}
