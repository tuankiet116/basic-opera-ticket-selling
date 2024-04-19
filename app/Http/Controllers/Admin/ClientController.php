<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(protected ClientService $clientService)
    {
    }

    public function getClientsSpecial()
    {
        $searchStr = request()->get("search");
        $clients = $this->clientService->getClientsSpecial($searchStr);
        dd($clients);
    }
}
