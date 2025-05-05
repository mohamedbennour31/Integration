<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GeminiService
{
  private HttpClientInterface $httpClient;
  private string $apiKey;
  private string $model;

  public function __construct(
    HttpClientInterface $httpClient,
    ParameterBagInterface $params
  ) {
    $this->httpClient = $httpClient;
    $this->apiKey = $params->get('google_ai_api_key');
    $this->model = $params->get('google_ai_model', 'gemini-1.0-pro');
  }

  public function generateContent(string $prompt): string
  {
    $url = sprintf(
      'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
      $this->model,  // Will use gemini-2.0-flash from .env
      $this->apiKey
    );

    $response = $this->httpClient->request('POST', $url, [
      'json' => [
        'contents' => [
          [
            'parts' => [
              ['text' => $prompt]
            ]
          ]
        ]
      ],
      'timeout' => 30
    ]);

    $data = $response->toArray();

    if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
      throw new \RuntimeException(
        $data['error']['message'] ?? 'Invalid response structure'
      );
    }

    return $data['candidates'][0]['content']['parts'][0]['text'];
  }
}
