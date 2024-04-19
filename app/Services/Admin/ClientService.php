<?php

namespace App\Services\Admin;

use App\Models\ClientModel;

class ClientService
{
    public function getClientsSpecial(string $searchStr = "")
    {
        return ClientModel::search($searchStr)
            ->where("isSpecial", false)->paginate(PAGINATE_NUMBER);
    }
}
