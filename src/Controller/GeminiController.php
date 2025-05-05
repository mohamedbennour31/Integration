<?php

namespace App\Controller;

use App\Service\GeminiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class GeminiController extends AbstractController
{
  #[Route('/gemini', name: 'gemini_ui', methods: ['GET'])]
  public function index(): Response
  {
    return $this->render('evaluation/gemini.html.twig');
  }

  #[Route('/api/gemini', name: 'app_gemini', methods: ['POST'])]
  public function generate(Request $request, GeminiService $gemini): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $prompt = $data['prompt'] ?? '';

    if (empty($prompt)) {
      return $this->json(['error' => 'Prompt is required'], Response::HTTP_BAD_REQUEST);
    }

    try {
      $response = $gemini->generateContent($prompt);
      return $this->json(['response' => $response]);
    } catch (\Exception $e) {
      return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  #[Route('/api/gemini/debug', name: 'gemini_debug')]
  public function debug(GeminiService $geminiService, LoggerInterface $logger): Response
  {
    try {
      $response = $geminiService->generateContent('Tell me a joke');
      $logger->info('Gemini response', ['response' => $response]);
      return new Response($response);
    } catch (\Exception $e) {
      $logger->error('Gemini error', ['error' => $e->getMessage()]);
      return new Response('Error: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
