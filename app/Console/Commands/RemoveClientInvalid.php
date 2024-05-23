<?php

namespace App\Console\Commands;

use App\Models\BookModel;
use App\Models\ClientModel;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RemoveClientInvalid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-client-invalid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $clients = ClientModel::selectRaw("clients.*")->join("books", "books.client_id", "=", "clients.id", "left")
                ->where("isSpecial", false)->where("books.seat_id", null)->get();
            if (sizeof($clients)) {
                Log::info("Deleted clients: ", $clients->pluck("email")->toArray());
                $clients->each(function ($client) {
                    $client->delete();
                });
            }
        } catch (Exception $e) {
            Log::error("Error on remove client invalid command: ". $e->getMessage());
        }
    }
}
