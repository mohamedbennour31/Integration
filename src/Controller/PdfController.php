<?php

namespace App\Controller;

use App\Repository\EvaluationRepository;
use App\Repository\VoteRepository;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
  #[Route('/evaluations/pdf', name: 'evaluation_pdf')]
  public function exportToPdf(Pdf $knpSnappyPdf, EvaluationRepository $evaluationRepository): Response
  {
    $evaluations = $evaluationRepository->findAll();

    $html = $this->renderView('evaluation/evaluationpdf.html.twig', [
      'evaluations' => $evaluations,
    ]);

    $pdfContent = $knpSnappyPdf->getOutputFromHtml($html);

    return new Response($pdfContent, 200, [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => 'inline; filename="evaluations.pdf"',
    ]);
  }
  #[Route('/votes/pdf', name: 'vote_pdf')]
  public function generateVotePdf(Pdf $knpSnappyPdf, VoteRepository $voteRepository): Response
  {
    $votes = $voteRepository->findAll();

    $html = $this->renderView('evaluation/votepdf.html.twig', [
      'votes' => $votes,
    ]);

    $pdfContent = $knpSnappyPdf->getOutputFromHtml($html);

    return new Response($pdfContent, 200, [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => 'inline; filename="votes.pdf"',
    ]);
  }
}
