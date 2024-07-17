<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileModel extends Model
{
    use HasFactory;

    protected $table = "files";
    protected $fillable = [
        "file_name",
        "is_exported",
        "is_failed",
        "reason",
        "events",
        "start_date",
        "end_date"
    ];

    protected $casts = [
        "is_failed" => "boolean",
        "is_exported" => "boolean",
        "events" => "array"
    ];
}
