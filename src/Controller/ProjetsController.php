<?php

namespace App\Controller;

use App\Entity\Hackathon;
use App\Entity\Projets;
use App\Form\ProjetsType;
use App\Repository\ProjetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjetsController extends AbstractController
{
    #[Route('/projets', name: 'app_projets')]
    public function index(Request $request , EntityManagerInterface $em): Response
    {
        $projet = new Projets();
        $form = $this->createForm(ProjetsType::class, $projet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($projet);
            $em->flush();

            return $this->redirectToRoute('app_projets'); 
        }

        return $this->render('projets/index.html.twig', [
            'form' => $form->createView(),
        ]);
    
    }
    #[Route('hackathon/{id}/projets', name: 'voir_projets')]
    public function voirProjets(Hackathon $hackathon, ProjetsRepository $projets_repository): Response
    {
        $projets = $projets_repository->findBy(['id_hack' => $hackathon]);
    
        return $this->render('projets/afficherProjets.html.twig', [
            'hackathon' => $hackathon,
            'projets' => $projets,
        ]);
    }
}
