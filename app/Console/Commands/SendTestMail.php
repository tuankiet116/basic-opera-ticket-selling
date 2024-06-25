<?php

namespace App\Console\Commands;

use App\Mail\TestMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-test-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test mail to developer\'s email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dump(config("mail.mailers.smtp"));
        dump(config("mail.from"));
        Mail::to("kietdat1612000@gmail.com")->queue(new TestMail());
    }
}
