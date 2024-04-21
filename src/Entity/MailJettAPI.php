<?php

namespace App\Entity;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class MailJettAPI
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function send(string $recipient, string $sender, string $code): void
    {
        $mailjetApiKey = "dc4dcc4442be8f232d88ae3820edc0f5";
        $mailjetSecretKey = "52ff90665f96dfecf6f22f6bb1efa0df";
        $options = [
            'auth_basic' => [$mailjetApiKey, $mailjetSecretKey],
        ];

        $data = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $sender,
                        'Name' => 'PlayMate'
                    ],
                    'To' => [
                        [
                            'Email' => $recipient
                        ]
                    ],
                    'Subject' => 'Confirmation de paiement !',
                    'TextPart' => 'Chers clients, bienvenue à PlayMate! Nous avons le plaisir de vous confirmer votre réservation! ' . $code,
                    'HTMLPart' => '<h3>Chers clients, bienvenue à PlayMate!</h3> <br /> Nous avons le plaisir de vous confirmer votre réservation ! <h3>' . $code . '</h3>',
                ]
            ]
        ];

        try {
            $response = $this->httpClient->request('POST', 'https://api.mailjet.com/v3.1/send', [
                'auth_basic' => [$mailjetApiKey, $mailjetSecretKey],
                'json' => $data,
            ]);

            // Read the response data and status
            echo $response->getStatusCode();
            echo $response->getContent();
        } catch (TransportExceptionInterface $e) {
            echo "Mailjet Exception: " . $e->getMessage();
        }
    }
}
