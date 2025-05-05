<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjetRepository;

class ProjectController extends AbstractController
{
  #[Route('/projects', name: 'project_list')]
  public function listProjects(ProjetRepository $projectRepository): Response
  {
    $projects = $projectRepository->findAll();

    return $this->render('evaluation/projets.html.twig', [
      'projects' => $projects,
    ]);
  }
}
