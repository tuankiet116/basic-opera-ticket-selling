<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventModel extends Model
{
    use HasFactory;

    protected $table = "events";
    protected $fillable = [
        "name",
        "date",
        "description",
        "image_url"
    ];

    public function scopeSearchBy(Builder $query, string $search) {
        if (!$search) return $query;
        $search = strtolower($search);
        return $query->whereRaw("LOWER(name) LIKE ?", ["%$search%"]);
    }

    public function ticketClasses() {
        return $this->hasMany(TicketClassModel::class, "event_id", "id");
    }
}
