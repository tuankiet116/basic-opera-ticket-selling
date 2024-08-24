<?php

namespace App\Services\Admin;

use App\Models\BookModel;
use App\Models\ClientModel;
use App\Models\EventModel;
use App\Models\EventSeatClassModel;
use App\Models\TicketClassModel;
use App\Services\Common\ConvertImage;
use Carbon\Carbon;
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
        $time = Carbon::now()->format("Y-m-d H:i:s");
        DB::beginTransaction();
        try {
            $image = request()->file("image");
            $filePath = Storage::disk("public")->putFileAs("/events", $image, time());
            $resultConvert = app(ConvertImage::class)->webpConvert2(storage_path("app/public/" . $filePath));
            if ($resultConvert) {
                data_set($data, "image_url", $filePath . "webp");
            } else {
                Storage::disk("public")->delete($filePath);
                $filePath = Storage::disk("public")->putFileAs("/events", $image, time() . "." . $image->extension());
                data_set($data, "image_url", $filePath);
            }
            $event = EventModel::create($data);
            $ticketClassesData = array_map(fn($data) => $data + [
                "event_id" => $event->id,
                "created_at" => $time
            ], $data["ticketClasses"]);
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

    public function edit($eventId): array
    {
        $event = EventModel::with("ticketClasses")->unDeleted()->find($eventId);
        $event->ticketClasses->each(function (TicketClassModel &$ticketclass) use ($eventId) {
            $booking = BookModel::where([
                "ticket_class_id" => $ticketclass->id,
                "event_id" => $eventId,
                "is_client_special" => false
            ])->first();
            if ($booking) data_set($ticketclass, "isBooked", true);
            else data_set($ticketclass, "isBooked", false);
        });
        if (!$event) return [];
        return $event->toArray();
    }

    public function update(array $data, int $eventId)
    {
        $event = EventModel::unDeleted()->find($eventId);
        if (!$event) throw new NotFoundHttpException("Event not found");
        $time = Carbon::now()->format("Y-m-d H:i:s");
        DB::beginTransaction();
        try {
            $image = request()->file("image");
            if ($image) {
                if ($event->image_url) Storage::disk("public")->delete($event->image_url);
                $filePath = Storage::disk("public")->putFileAs("/events", $image, time());
                $resultConvert = app(ConvertImage::class)->webpConvert2(storage_path("app/public/" . $filePath));
                if ($resultConvert) {
                    data_set($data, "image_url", $filePath . ".webp");
                } else {
                    Storage::disk("public")->delete($filePath);
                    $filePath = Storage::disk("public")->putFileAs("/events", $image, time() . "." . $image->extension());
                    data_set($data, "image_url", $filePath);
                }
            }
            $event->update($data);
            $event->save();

            $ticketClasses = TicketClassModel::where("event_id", $eventId)->get();
            $ticketClasses->each(function ($ticketClass) use ($data, $eventId) {
                $dataTicket = collect($data['ticketClasses'])->where("id", $ticketClass->id)->first();
                $ticketClassBooked = BookModel::where([
                    "ticket_class_id" => $ticketClass->id,
                    "event_id" => $eventId
                ])->first();
                if ($ticketClassBooked && !$dataTicket) throw new Exception("Hạng vé đã được đặt chỗ, không thể xóa bỏ!");
                if ($ticketClassBooked && $dataTicket && $dataTicket["price"] != $ticketClass->price)
                    throw new Exception("Hạng vé đã được đặt chỗ, không thể thay đổi giá vé!");
                if ($ticketClassBooked && $dataTicket && $dataTicket["name"] != $ticketClass->name)
                    throw new Exception("Hạng vé đã được đặt chỗ, không thể thay đổi tên!");
                if (!$dataTicket) {
                    EventSeatClassModel::where(["event_id" => $eventId, "ticket_class_id" => $ticketClass->id])->delete();
                    $ticketClass->delete();
                } else {
                    $ticketClass->update($dataTicket);
                    $ticketClass->save();
                }
            });
            $dataTicketInsertable = collect($data['ticketClasses'])->where("id", null)->map(function ($ticketClass) use ($eventId, $time) {
                $ticketClass["event_id"] = $eventId;
                $ticketClass["created_at"] = $time;
                return $ticketClass;
            })->all();
            if ($dataTicketInsertable) {
                TicketClassModel::insert($dataTicketInsertable);
            }
        } catch (Exception $e) {
            Log::error("Update Event: ", [
                "message" => $e->getMessage()
            ]);
            if (isset($filePath) && $filePath) Storage::disk("public")->delete($filePath);
            DB::rollBack();
            throw $e;
        }
        DB::commit();
        return $event;
    }

    public function updateStatus(array $data, int $eventId)
    {
        $event = EventModel::unDeleted()->find($eventId);
        if (!$event) throw new NotFoundHttpException("Event not found");
        $event->update($data);
        $event->save();
        return $event;
    }

    public function deleteEvent(int $eventId)
    {
        $event = EventModel::unDeleted()->find($eventId);
        if (!$event) throw new NotFoundHttpException("Delete event not availabled");
        $event->update(["is_delete" => true]);
        $event->save();
        return true;
    }
}
