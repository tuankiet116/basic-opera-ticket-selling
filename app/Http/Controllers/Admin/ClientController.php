<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateSpecialClientRequest;
use App\Services\Admin\ClientService;

class ClientController extends Controller
{
    public function __construct(protected ClientService $clientService)
    {
    }

    public function getClientsSpecial()
    {
        $searchStr = request()->get("search") ?? "";
        $isPaginate = request()->get("isPaginate") == "true" ? true : false;
        $clients = $this->clientService->getClients($searchStr, true, $isPaginate)->toArray();
        return $this->responseSuccess($clients);
    }

    public function getClients()
    {
        $searchStr = request()->get("search") ?? "";
        $clients = $this->clientService->getClients($searchStr, false)->toArray();
        return $this->responseSuccess($clients);
    }

    public function createClientSpecial(CreateSpecialClientRequest $request)
    {
        $data = $request->validated();
        $client = $this->clientService->createClient($data, true);
        if ($client) return $this->responseSuccess($client);
        return $this->responseError([
            "message" => __("messages.errors.common")
        ]);
    }

    public function updateClientSpecial(CreateSpecialClientRequest $request, int $clientId)
    {
        $data = $request->validated();
        $client = $this->clientService->updateClient($data, $clientId, true);
        if ($client) return $this->responseSuccess($client);
        return $this->responseError([
            "message" => __("messages.errors.common")
        ]);
    }

    public function edit(int $clientId)
    {
        $client = $this->clientService->getClient($clientId);
        if (!$client) return $this->responseError([
            "message" => __("messages.errors.common")
        ]);
        return $this->responseSuccess($client->toArray());
    }

    public function delete(int $clientId)
    {
        $result = $this->clientService->deleteClient($clientId);
        if ($result) return $this->responseSuccess();
        return $this->responseError();
    }
}
