<?php

namespace App\Console\Commands;

use App\Mail\PaymentMail;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-mail';

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
    $payment = Payment::where('status', 'pending')->first();

    if (!$payment) {
        $this->error("No pending payment found.");
        return 1;
    }

    Mail::to("mnikezwe@gmail.com")->send(new PaymentMail($payment));
    $this->info("Payment email sent successfully for payment ID {$payment->id}.");

    return 0;
}
}
