<?php

namespace App\Console\Commands;

use App\Models\BookModel;
use Illuminate\Console\Command;

class AddPricingFromTicketToBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-pricing-from-ticket-to-booking';

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
        $bookings = BookModel::get();
        
    }
}
