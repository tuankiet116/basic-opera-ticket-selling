<?php

namespace App\Services\Admin;

use App\Events\ClientRemoveBookingTicket;
use App\Models\BookModel;
use App\Models\ClientModel;
use App\Models\EventModel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientService
{
    public function getClients(string $searchStr = "", $isSpecial = true, $isPaginate = true)
    {
        return ClientModel::search($searchStr)
            ->where("isSpecial", $isSpecial)->when($isPaginate, function ($q) {
                return $q->orderBy("created_at", "desc")->paginate(PAGINATE_NUMBER);
            }, function ($q) {
                return $q->orderBy("created_at", "desc")->get();
            });
    }

    public function createClient(array $data, $special = true)
    {
        $data["isSpecial"] = $special;
        try {
            $data["banking_code"] = time();
            $client = ClientModel::create($data);
            $client = $client->toArray();
        } catch (\Exception $e) {
            $client = null;
            Log::error("Create Event: ", [
                "data" => collect($data)->except("image"),
                "message" => $e->getMessage()
            ]);
        }
        return $client;
    }

    public function updateClient(array $data, int $clientId, $special = true)
    {
        try {
            $client = ClientModel::find($clientId);
            if ($client->isSpecial != $special) {
                throw new \Exception("Cannot update special state of client");
            }
            $client->update($data);
            $client = $client->toArray();
        } catch (\Exception $e) {
            $client = null;
            Log::error("Update Client: ", [
                "data" => $data,
                "client_id" => $clientId,
                "message" => $e->getMessage()
            ]);
        }
        return $client;
    }

    public function getClient(int $clientId)
    {
        $client = ClientModel::find($clientId);
        return $client;
    }

    public function deleteClient(int $clientId)
    {
        $client = ClientModel::where("isSpecial", true)->find($clientId);
        if ($client) {
            $seatsRemoveByEvent = [];
            $bookings = BookModel::with("seat")->where("client_id", $clientId)->get();
            $bookings->each(function ($booking) use (&$seatsRemoveByEvent) {
                if (!isset($seatsRemoveByEvent[$booking->event_id])) $seatsRemoveByEvent[$booking->event_id] = [];
                $seatsRemoveByEvent[$booking->event_id][] = $booking->seat;
            });

            foreach ($seatsRemoveByEvent as $eventId => $seats) {
                $event = EventModel::find($eventId);
                $seatsChunked = array_chunk($seats, CHUNK_SIZE_BROADCAST);
                foreach ($seatsChunked as $chunked) {
                    ClientRemoveBookingTicket::dispatch($chunked, $event);
                }
            }
            DB::beginTransaction();
            try {
                BookModel::where("client_id", $clientId)->delete();
                ClientModel::where("isSpecial", true)->where("id", $clientId)->delete();
            } catch (Exception $e) {
                DB::rollBack();
                Log::error("Error on delete client specials: " . $e->getMessage());
                return false;
            }
            DB::commit();
        }

        return true;
    }
}
