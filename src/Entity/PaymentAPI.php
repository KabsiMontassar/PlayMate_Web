<?php

namespace App\Entity;

use App\Entity\Payment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class PaymentAPI
{
    private const API_URL = 'https://api.preprod.konnect.network/api/v2/payments/';
    private const API_KEY = '65e2061d0ed588b99337c12b:Hphdip8xVt5KwPOPyAfgVv1H2n';
    private $paymentRef = "";
    private HttpClientInterface $client;
    private $entityManager;

    private $router;
    public function __construct(HttpClientInterface $client, RouterInterface $router)
    {
        $this->client = $client;
        $this->router = $router;
    }

    public function checkPayment(string $paymentId): bool
    {
        try {
            $response = $this->client->request('GET', self::API_URL . $paymentId, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-api-key' => self::API_KEY,
                ],
            ]);

            $data = $response->toArray(); // Converts JSON to array
            return $data['payment']['successfulTransactions'] === 1;
        } catch (\Exception $e) {
            // Log error or handle it as required
            return false;
        }
    }

    public function initPayment(Payment $payment, float $prix): ?array
    {
        try {
            $response = $this->client->request('POST', self::API_URL . 'init-payment', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-api-key' => self::API_KEY,
                ],
                'json' => $this->buildJsonPayload($payment, $prix),
            ]);

            $data = $response->toArray();
            //var_dump($data);
            return [
                'payUrl' => $data['payUrl'] ?? '',
                'paymentId' => $data['paymentRef'] ?? '',
            ];
        } catch (\Exception $e) {
            var_dump($e);
            return null;
        }
    }

    private function buildJsonPayload(Payment $payment, float $prix): array
    {
        $price = $prix * 1000;
        return [
            'receiverWalletId' => '65e2061d0ed588b99337c12f',
            'token' => 'TND',
            'amount' => $price,
            'description' => 'Payment for PlayMate',
            'acceptedPaymentMethods' => ['wallet', 'bank_card', 'e-DINAR'],
            'firstName' => $payment->getIdmembre()->getName(),
            'lastName' => "",
            'email' => $payment->getIdmembre()->getEmail(),
            'orderId' => 65841125,
            'successUrl' => $this->router->generate('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            //'failUrl' => $this->router->generate('payment_fail', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];
    }
}
