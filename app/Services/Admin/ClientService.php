<?php

namespace App\Services\Admin;

use App\Models\ClientModel;
use Illuminate\Support\Facades\Log;

class ClientService
{
    public function getClientsSpecial(string $searchStr = "", $isSpecial = true)
    {
        return ClientModel::search($searchStr)
            ->where("isSpecial", $isSpecial)->paginate(PAGINATE_NUMBER);
    }

    public function createClient(array $data, $special = true)
    {
        $data["isSpecial"] = $special;
        try {
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
}
