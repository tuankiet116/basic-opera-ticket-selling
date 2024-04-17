<?php

namespace App\Services\Admin;

use App\Models\EventModel;
use App\Models\TicketClassModel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventService
{
    public function listAll(string $query = "")
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
            $ticketClassesData = array_map(fn($data) => $data + ["event_id" => $event->id], $data["ticketClasses"]);
            TicketClassModel::insert($ticketClassesData);
        } catch (Exception $e) {
            Log::error("Create Event: ", [
                "data" => collect($data)->except("image"),
                "message" => $e->getMessage()
            ]);
            if (isset($filePath) && $filePath) Storage::disk("public")->delete($filePath);
            DB::rollBack();
            return null;
        }
        DB::commit();
        return $event;
    }

    public function edit($eventId): EventModel | null
    {
        $event = EventModel::with("ticketClasses")->find($eventId);
        if (!$event) return null;
        return $event;
    }

    public function update(array $data, int $eventId)
    {
        $event = EventModel::find($eventId);
        if (!$event) throw new NotFoundHttpException("Event not found");
        DB::beginTransaction();
        try {
            $image = request()->file("image");
            if ($image) {
                if ($event->image_url) Storage::disk("public")->delete($event->image_url);
                $filePath = Storage::disk("public")->putFileAs("/events", $image, time() . ".png");
                data_set($data, "image_url", $filePath);
            }
            $event->update($data);
            $event->save();

            $ticketClasses = TicketClassModel::where("event_id", $eventId)->get();
            $ticketClasses->each(function ($ticketClass) use ($data) {
                $dataTicket = collect($data['ticketClasses'])->where("id", $ticketClass->id)->first();
                if (!$dataTicket) {
                    $ticketClass->delete();
                } else {
                    $ticketClass->update($dataTicket);
                    $ticketClass->save();
                }
            });
            $dataTicketInsertable = collect($data['ticketClasses'])->where("id", null)->map(function($ticketClass) use($eventId) {
                $ticketClass["event_id"] = $eventId;
                return $ticketClass;
            })->all();
            if ($dataTicketInsertable) {
                TicketClassModel::insert($dataTicketInsertable);
            }
        } catch (Exception $e) {
            Log::error("Update Event: ", [
                "data" => collect($data)->except("image"),
                "message" => $e->getMessage()
            ]);
            if (isset($filePath) && $filePath) Storage::disk("public")->delete($filePath);
            DB::rollBack();
            return null;
        }
        DB::commit();
        return $event;
    }
}
