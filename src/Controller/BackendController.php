<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BackendController extends AbstractController
{
  #[Route('/backend', name: 'backend_dashboard')]
  public function index(): Response
  {
    return $this->render('backend.html.twig');
  }
}
