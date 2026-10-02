<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class WhatsAppService
{
    public function send(string $phone, string $message): void
    {
        $sid = (string) config('services.twilio.sid'); $token = (string) config('services.twilio.token'); $from = (string) config('services.twilio.whatsapp_from');
        if ($sid === '' || $token === '' || $from === '') throw new RuntimeException('Twilio WhatsApp no está configurado.');
        Http::asForm()->withBasicAuth($sid, $token)->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", ['From' => "whatsapp:{$from}", 'To' => "whatsapp:{$phone}", 'Body' => $message])->throw();
    }
}
