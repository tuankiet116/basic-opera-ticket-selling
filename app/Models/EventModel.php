<?php

namespace App\Models;

use Carbon\Carbon;
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
        "image_url",
        "is_openning",
        "is_delete",
        "banking_code",
    ];

    protected $casts = [
        "date" => "date",
        "is_openning" => "boolean",
        "is_delete" => "boolean",
    ];

    public function scopeSearchBy(Builder $query, string $search)
    {
        if (!$search) return $query->where("is_delete", false);
        $search = strtolower($search);
        return $query->whereRaw("LOWER(name) LIKE ?", ["%$search%"])->where("is_delete", false);
    }

    public function scopeAvailable(Builder $query)
    {
        return $query->where("is_openning", true)->where("is_delete", false);
    }

    public function scopeUnDeleted(Builder $query)
    {
        return $query->where("is_delete", false);
    }

    public function ticketClasses()
    {
        return $this->hasMany(TicketClassModel::class, "event_id", "id");
    }
}
