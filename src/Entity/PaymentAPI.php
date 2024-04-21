<?php

namespace App\Entity;

use App\Entity\Payment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PaymentAPI
{
    private const API_URL = 'https://api.preprod.konnect.network/api/v2/payments/init-payment';
    private const API_KEY = '65e2061d0ed588b99337c12b:lfeWlefzQi2IKdIYGZa5vjfCh9jDdbR';
    private $paymentRef = "";

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function getPaymentRef(): ?string
    {
        return $this->paymentRef;
    }

    public function initPayment(Payment $paiement, int $prix): ?string
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', self::API_URL, [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-api-key' => self::API_KEY,
            ],
            'body' => $this->buildJsonPayload($paiement, $prix),
        ]);

        try {
            $content = $response->getContent();
            $paymentRef = $this->extractPaymentRef($content);
            return $paymentRef;
        } catch (TransportExceptionInterface $e) {
            // Gérer les erreurs de requête HTTP
            return null;
        }
    }

    private function extractPaymentRef(string $response): string
    {
        $responseData = json_decode($response, true);
        return $responseData['paymentRef'] ?? '';
    }

    private function buildJsonPayload(Payment $paiement, int $prix): string
    {
        // Construire la charge JSON basée sur les propriétés de l'objet Paiement
        // Exemple de charge utile. Adapter les champs en fonction des propriétés de votre Paiement et des exigences de l'API
        $userId = $paiement->getIdmembre();
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $firstName = $user ? $user->getName() : '';
        $email = $user ? $user->getEmail() : '';

        $price = $prix * 1000;
        return json_encode([
            'receiverWalletId' => '65e2061d0ed588b99337c12f',
            'token' => 'TND',
            'amount' => $price,
            'description' => 'Payment for playmate',
            'acceptedPaymentMethods' => ['wallet', 'bank_card'],
            'firstName' => $firstName,
            'lastName' => 'NoLastName',
            'email' => $email,
            'orderId' => '658435', // Remplacer par un identifiant d'ordre réel
        ]);
    }



    public function extractPayUrlFromResponse(string $response): string
    {
        $jsonResponse = json_decode($response, true);
        return $jsonResponse['payUrl'] ?? '';
    }

    public function isPaymentSuccessful(): bool
    {
        try {
            $client = HttpClient::create();
            $response = $client->request('GET', "https://api.preprod.konnect.network/api/v2/payments/{$paymentRef}");

            if ($response->getStatusCode() === 200) {
                $responseData = $response->toArray();
                $successfulTransactions = $responseData['payment']['successfulTransactions'] ?? 0;
                return $successfulTransactions > 0;
            } else {
                // Gérer l'échec de la requête
                return false;
            }
        } catch (TransportExceptionInterface $e) {
            // Gérer les erreurs de transport
            return false;
        }
    }
}
