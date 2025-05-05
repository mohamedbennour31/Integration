<?php

namespace App\Service;

use Twilio\Rest\Client;

class TwilioService
{
    private $sid;
    private $authToken;
    private $fromNumber;

    public function __construct(string $sid, string $authToken, string $fromNumber)
    {
        $this->sid = $sid;
        $this->authToken = $authToken;
        $this->fromNumber = $fromNumber; // Assure-toi que cette ligne existe
    }

    public function sendSms(string $to, string $message): void
    {
        $client = new Client($this->sid, $this->authToken);
        $client->messages->create(
            $to,
            [
                'from' => $this->fromNumber, // Utilise $fromNumber ici
                'body' => $message
            ]
        );
    }
}
