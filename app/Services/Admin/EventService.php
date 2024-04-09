<?php

namespace App\Services\Admin;

use App\Models\EventModel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EventService
{
    public function listAll(string $query = ""): array
    {
        return EventModel::searchBy($query)->paginate(PAGINATE_NUMBER);
    }

    public function create(array $data): EventModel | null
    {
        DB::beginTransaction();
        try {
            $image = request()->file("image");
            $filePath = Storage::disk("public")->putFileAs("/events", $image, time() . ".png");
            data_set($data, "image_url", $filePath);
            $event = EventModel::create($data);
        } catch (Exception $e) {
            Log::error("Create Event: ", [
                "data" => collect($data)->except("image"),
                "message" => $e->getMessage()
            ]);
            DB::rollBack();
            return null;
        } finally {
            DB::commit();
            return $event;
        }
    }
}
