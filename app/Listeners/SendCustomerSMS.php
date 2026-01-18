<?php

namespace App\Listeners;

use App\Events\SMSNotification;
use App\Repositories\SMSRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCustomerSMS
{
    public function __construct()
    {
        //
    }

    public function handle(SMSNotification $event): void
    {
        // Send sms to customer
        $phoneNumber = $event->phoneNumber;
        $message     = $event->message;
        $phoneNumber = "88" . $phoneNumber;

        if ($phoneNumber && $message) {
            $sms = new SMSRepository();
            $sms->sendSMS($phoneNumber, $message);
        }
    }
}
