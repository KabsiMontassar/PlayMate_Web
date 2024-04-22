<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client, $apiKey)
    {
        $this->client = $client;
        $this->apiKey = 'c6147ca1bcd86843a87c6f7a0a40673e'; // Enclose the API key in quotes
    }

    public function getWeatherForecast($city)
    {

    
        $response = $this->client->request('GET', 'http://api.openweathermap.org/data/2.5/forecast', [
            'query' => [
                'q' => $city,
                'appid' => $this->apiKey,
                'units' => 'metric', // Pour avoir la température en degrés Celsius
                'cnt' => 40 // Nombre de prévisions de 3 heures, 40 couvre 5 jours
            ]
        ]);

        return $response->toArray();
       
    }
}
