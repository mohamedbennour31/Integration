<?php

namespace App\Controller;

use App\Entity\Communaute;
use App\Form\CommunauteType;
use App\Repository\CommunauteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/backoffice/communaute')]
class BackCommunauteController extends AbstractController
{
    #[Route('/', name: 'app_communaute_back', methods: ['GET'])]
    public function showBack(CommunauteRepository $communauteRepository): Response
    {
        $communautes = $communauteRepository->findAll();

        return $this->render('backoffice/communautes/show.html.twig', [
            'communautes' => $communautes,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_communaute_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Communaute $communaute, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CommunauteType::class, $communaute);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Communauté modifiée avec succès.');
            return $this->redirectToRoute('app_communaute_back');
        }
    
        return $this->render('backoffice/communautes/edit.html.twig', [
            'form' => $form->createView(),
            'communaute' => $communaute,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_communaute_delete', methods: ['POST'])]
    public function delete(Request $request, Communaute $communaute, EntityManagerInterface $em): Response
    {
        // Debug info
        $expectedToken = 'delete' . $communaute->getId();
        $receivedToken = $request->request->get('_token');
        $isValid = $this->isCsrfTokenValid('delete'.$communaute->getId(), $receivedToken);
        
        $this->addFlash('info', 'Debug - ID: ' . $communaute->getId() . ', Expected Token: ' . $expectedToken . ', Received: ' . $receivedToken . ', Valid: ' . ($isValid ? 'Yes' : 'No'));
        
        if ($isValid) {
            try {
                // Get the community name and ID for the success message
                $communauteName = $communaute->getNom();
                $communauteId = $communaute->getId();
                
                // Debug before deletion
                $this->addFlash('info', 'Attempting to delete community: ' . $communauteName . ' (ID: ' . $communauteId . ')');
                
                // Alternate direct delete approach - try a direct SQL delete
                $connection = $em->getConnection();
                $sql = 'DELETE FROM communaute WHERE id = :id';
                $result = $connection->executeStatement($sql, ['id' => $communauteId]);
                
                if ($result > 0) {
                    $this->addFlash('success', sprintf('La communauté "%s" a été supprimée avec succès via SQL direct. Tous les chats, messages, sondages et autres données associées ont également été supprimés.', $communauteName));
                } else {
                    // If SQL direct didn't work, try the normal approach
                    $em->remove($communaute);
                    $em->flush();
                    $this->addFlash('success', sprintf('La communauté "%s" a été supprimée avec succès via EntityManager. Tous les chats, messages, sondages et autres données associées ont également été supprimés.', $communauteName));
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression : ' . $e->getMessage());
                // Add stack trace for debugging
                $this->addFlash('info', 'Stack trace: ' . $e->getTraceAsString());
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide. Attendu: ' . $expectedToken . ', Reçu: ' . $receivedToken);
        }

        return $this->redirectToRoute('app_communaute_back', [], Response::HTTP_SEE_OTHER);
    }
}