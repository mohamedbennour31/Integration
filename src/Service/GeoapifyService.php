<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoapifyService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client, string $geoapifyKey)
    {
        $this->client = $client;
        $this->apiKey = $geoapifyKey;
    }

    public function getCoordinates(string $address): ?array
    {
        $response = $this->client->request('GET', 'https://api.geoapify.com/v1/geocode/search', [
            'query' => [
                'text' => $address,
                'apiKey' => $this->apiKey
            ]
        ]);

        $data = $response->toArray();

        if (!empty($data['features'])) {
            $coords = $data['features'][0]['geometry']['coordinates'];
            return ['lon' => $coords[0], 'lat' => $coords[1]];
        }

        return null;
    }
}
